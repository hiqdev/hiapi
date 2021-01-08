<?php

namespace hiapi\event;

use League\Event\AbstractListener;
use League\Event\EventInterface;
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
class PublishToQueueListener extends AbstractListener
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

    /**
     * @var int|null When number is passed, Priority queues are supported with the maximum priority as set.
     *  `null` disables priority queue support
     */
    public $maxPriority = null;

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

            $table = new \PhpAmqpLib\Wire\AMQPTable();
            if ($this->maxPriority !== null) {
                $table->set('x-max-priority', $this->maxPriority);
            }

            $channel->queue_declare($this->queue, false, true, false, false, false, $table);
        }

        return $this->channel;
    }

    private function createMessage($event): AMQPMessage
    {
        if (!$event instanceof \JsonSerializable) {
            throw new InvalidConfigException('Event "' . get_class($event) . '" can not be sent to queue');
        }

        $options = [];
        if ($this->maxPriority !== null && $event instanceof PriorityEventInterface) {
            if ($event->getPriority() > $this->maxPriority) {
                throw new InvalidConfigException('Event "' . get_class($event) . '" priority is above supported maximum');
            }

            $options['priority'] = $event->getPriority();
        }

        return new AMQPMessage(
            json_encode($event->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            array_merge([
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'content_type' => 'application/json',
            ], $options)
        );
    }
}
