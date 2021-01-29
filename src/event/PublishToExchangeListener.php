<?php

namespace hiapi\event;

use League\Event\AbstractListener;
use League\Event\EventInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

/**
 * Class PublishToExchangeListener published events to AMQP exchange.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PublishToExchangeListener extends AbstractListener
{
    /**
     * @var AMQPStreamConnection
     */
    protected $amqp;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var string the exchange name for the published messages
     */
    public $exchange;

    /**
     * @var string the exchange type. Defaults to 'direct'
     */
    public $exchangeType = 'direct';

    public function __construct(AMQPStreamConnection $amqp, LoggerInterface $logger)
    {
        $this->amqp = $amqp;
        $this->logger = $logger;
    }

    /**
     * Handle an event.
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event): void
    {
        if ($this->exchange === null) {
            throw new \RuntimeException('Property PublishToQueueListener::queue must be set');
        }

        try {
            $message = $this->createMessage($event);
            $this->getChannel()->basic_publish($message, $this->exchange, $this->buildRoutingKey($event));
        } catch (InvalidConfigException $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }

    protected function getChannel(): AMQPChannel
    {
        if ($this->channel === null) {
            $this->channel = $channel = $this->amqp->channel();
            $channel->exchange_declare($this->exchange, $this->exchangeType, false, true, true, false, false);
        }

        return $this->channel;
    }

    private function createMessage($event): AMQPMessage
    {
        if (!$event instanceof \JsonSerializable) {
            throw new InvalidConfigException('Event "' . get_class($event) . '" can not be sent to exchange');
        }

        return new AMQPMessage(json_encode($event->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json',
        ]);
    }

    /**
     * Builds routing key for $event. Default logic:
     *
     * For events named with `Was` (or `WillBe`) keyword (e.g. `ObjectWasChanged`) routing key will be `object.was.changed`.
     * For other events [[InvalidConfigException]] will be thrown.
     *
     * You can override this method to implement own routing key generation logic.
     *
     * @param EventInterface $event
     * @return string
     * @throws InvalidConfigException when `Was` keyword
     */
    public function buildRoutingKey(EventInterface $event)
    {
        $className = (new \ReflectionClass($event))->getShortName();

        foreach (['Was', 'WillBe', 'Will'] as $keyword) {
            if (strpos($className, $keyword) === false) {
                continue;
            }

            [$object, $eventName] = explode($keyword, $className);

            $object = Inflector::camel2id($object);
            $eventName = Inflector::camel2id($eventName);
            $lowerKeyword = strtolower($keyword);

            return mb_strtolower("$object.$lowerKeyword.$eventName");
        }

        throw new InvalidConfigException("Event class name \"$className\" does not contain \"Was\" or \"WillBe\" keywords and can not be processed with default logic.");
    }
}
