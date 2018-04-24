<?php

namespace hiapi\event;

use League\Event\EventInterface;
use League\Event\ListenerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use yii\base\InvalidConfigException;

/**
 * Class PublishToQueueListener
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PublishToQueueListener implements ListenerInterface
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
     * @var string the queue name for the published messages
     */
    public $queue;

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
        if ($this->queue === null) {
            throw new \RuntimeException('Property PublishToQueueListener::queue must be set');
        }

        try {
            $message = $this->createMessage($event);
            $this->getChannel()->basic_publish($message, '', $this->queue);
        } catch (InvalidConfigException $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }

    protected function getChannel(): AMQPChannel
    {
        if ($this->channel === null) {
            $this->channel = $channel = $this->amqp->channel();
            $channel->queue_declare($this->queue, false, true, false, false);
        }

        return $this->channel;
    }

    public function isListener($listener)
    {
        throw new \Exception('Report to @silverfire. This method should not be called.');
    }

    private function createMessage($event): AMQPMessage
    {
        if (!$event instanceof \JsonSerializable) {
            throw new InvalidConfigException('Event "' . get_class($event) . '" can not be sent to queue');
        }

        return new AMQPMessage(json_encode($event->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json',
        ]);
    }
}
