<?php
declare(strict_types=1);

namespace hiapi\Service\Queue;

use hiapi\exceptions\NotProcessableException;
use hiqdev\yii2\autobus\components\AutoBusFactoryInterface;
use hiqdev\yii2\autobus\components\AutoBusInterface;
use hiqdev\yii2\autobus\exceptions\WrongCommandException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Log\LoggerInterface;
use RuntimeException;
use yii\helpers\Console;

class QueueConsumerService
{
    public function __construct(
        protected AMQPStreamConnection $amqp,
        protected LoggerInterface $logger,
        protected AutoBusFactoryInterface $busFactory,
    ) {
    }

    /**
     * @param string $queueName
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    protected function createChannel(string $queueName): AMQPChannel
    {
        $channel = $this->amqp->channel();
        $channel->queue_declare($queueName, false, true, false, false);

        return $channel;
    }

    /**
     * @param string $queueName
     * @param int $maxProcessedMessagesCount
     */
    public function consume(string $queueName, int $maxProcessedMessagesCount = 100): void
    {
        $channel = $this->createChannel($queueName);
        $bus = $this->busFactory->get($queueName);

        Console::output(' [*] Waiting for messages. To exit press CTRL+C');

        $callback = function (AMQPMessage $msg) use (&$maxProcessedMessagesCount, $queueName, $channel, $bus) {
            Console::output(' [x] Received ' . $msg->body);
            $channel->basic_ack($msg->getDeliveryTag());
            $maxProcessedMessagesCount--;

            try {
                $this->handle($bus, $msg);
            } catch (NotProcessableException $e) {
                $this->requeue($queueName, $msg, $e);
            } catch (\Exception $e) {
                $this->handleError($queueName, $msg, $e);
            }
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($channel->callbacks && $maxProcessedMessagesCount > 0) {
            $channel->wait();
        }

        Console::output(' [x] Reached consumed messages limit. Stopping process.');
    }

    private function handleError(string $queueName, AMQPMessage $message, \Exception $exception): void
    {
        Console::error(' [E] Error: ' . $exception->getMessage());
        $this->logger->warning('Failed to handle message: ' . $exception->getMessage(), ['amqpMessage' => $message, 'exception' => $exception]);
        $this->storeRejected($queueName, $message, $exception);
    }

    /**
     * Decodes AMQP message and sends it to the handler
     * // TODO: move to separate class?
     *
     * @param AMQPMessage $msg
     * @throws WrongCommandException
     */
    protected function handle(AutoBusInterface $bus, AMQPMessage $msg): void
    {
        if ($msg->get_properties()['content_type'] !== 'application/json') {
            throw new RuntimeException('Do not know how to decode ' . $msg->getContentEncoding());
        }

        $body = json_decode($msg->getBody(), true);
        if (!isset($body['name'])) {
            throw new WrongCommandException('Message must have a name');
        }
        $parts = explode('\\', $body['name']);
        $name = array_pop($parts);

        $bus->runCommand($name, $body);
    }

    /**
     * Resends message to queue with a delay
     *
     * @param string $queueName
     * @param AMQPMessage $msg
     * @param NotProcessableException $exception
     */
    private function requeue(string $queueName, AMQPMessage $msg, NotProcessableException $exception): void
    {
        $tries = 0;
        $headers = $msg->get_properties()['application_headers'];
        if ($headers instanceof AMQPTable) {
            $tries = $headers->getNativeData()['x-number-of-tries'] ?? 0;
        }

        if ($exception->getMaxTries() !== null && $tries >= $exception->getMaxTries()) {
            $this->logger->debug('No tries left for message. Marking it as an error', ['amqpMessage' => $msg, 'exception' => $exception]);
            $this->handleError($queueName, $msg, $exception);
            return;
        }

        // Init delay exchange
        $channel = $this->amqp->channel();
        $delayExchange = "$queueName.delayed";
        $channel->exchange_declare($delayExchange, 'x-delayed-message', false, true, true, false, false, new AMQPTable([
            'x-delayed-type' => 'direct',
        ]));
        $channel->queue_bind($queueName, $delayExchange);

        // Send message
        $delayDuration = 1000 * $exception->getSecondsBeforeRetry() * (int)($exception->getProgressionMultiplier() ** $tries);
        $delayMessage = new AMQPMessage($msg->getBody(), array_merge($msg->get_properties(), [
            'application_headers' => new AMQPTable([
                'x-delay' => $delayDuration,
                'x-number-of-tries' => $tries + 1,
            ]),
        ]));
        $channel->basic_publish($delayMessage, $delayExchange, '');
        $this->logger->debug('Delayed message for ' . $delayDuration . 'ms', ['amqpMessage' => $msg, 'exception' => $exception]);
    }

    private function storeRejected(string $queueName, AMQPMessage $message, \Exception $exception): void
    {
        $channel = $this->amqp->channel();
        $failedExchange = "$queueName.failed";

        $channel->exchange_declare($failedExchange, 'fanout', false, true, false);
        $channel->basic_publish($message, $failedExchange);
    }

}
