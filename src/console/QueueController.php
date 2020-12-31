<?php

namespace hiapi\console;

use hiapi\exceptions\NotProcessableException;
use hiqdev\yii2\autobus\components\AutoBusFactoryInterface;
use hiqdev\yii2\autobus\components\AutoBusInterface;
use hiqdev\yii2\autobus\exceptions\WrongCommandException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Log\LoggerInterface;
use yii\base\Module;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class QueueController
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class QueueController extends \yii\console\Controller
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var AMQPStreamConnection
     */
    protected $amqp;
    /**
     * @var AutoBusFactoryInterface
     */
    private $busFactory;

    public function __construct(
        $id,
        Module $module,
        AMQPStreamConnection $amqp,
        LoggerInterface $logger,
        AutoBusFactoryInterface $busFactory,
        array $config = []
    ) {
        $this->logger = $logger;
        $this->amqp = $amqp;
        $this->busFactory = $busFactory;

        parent::__construct($id, $module, $config);
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    private function createChannel(string $queue): AMQPChannel
    {
        $channel = $this->amqp->channel();
        $channel->queue_declare($queue, false, true, false, false);

        return $channel;
    }

    /**
     * @param string $queueName
     * @param int $messagesCount
     * @return int
     */
    public function actionConsume(string $queueName, $messagesCount = 100)
    {
        $channel = $this->createChannel($queueName);
        $bus = $this->busFactory->get($queueName);

        Console::output(' [*] Waiting for messages. To exit press CTRL+C');

        $callback = function (AMQPMessage $msg) use (&$messagesCount, $queueName, $channel, $bus) {
            Console::output(' [x] Received ' . $msg->body);
            $channel->basic_ack($msg->delivery_info['delivery_tag']);
            $messagesCount--;

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

        while ($channel->callbacks && $messagesCount > 0) {
            $channel->wait();
        }

        Console::output(' [x] Reached consumed messages limit. Stopping process.');

        return ExitCode::OK;
    }

    private function handleError(string $queueName, AMQPMessage $message, \Exception $exception)
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
            throw new \RuntimeException('Do not know how to decode ' . $msg->getContentEncoding());
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
        $delayDuration = 1000 * $exception->getSecondsBeforeRetry() * (int)pow($exception->getProgressionMultiplier(), $tries);
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
        // TODO: store $exception as well
        $channel = $this->createChannel("$queueName.failed");
        $channel->basic_publish($message, "$queueName.failed");
    }
}
