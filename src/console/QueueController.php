<?php
declare(strict_types=1);

namespace hiapi\console;

use hiapi\Service\Queue\QueueConsumerService;
use yii\base\Module;
use yii\console\ExitCode;

/**
 * Class QueueController
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class QueueController extends \yii\console\Controller
{
    public function __construct($id, Module $module, private readonly QueueConsumerService $consumer, array $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $queueName
     * @param int $messagesCount
     * @return int
     */
    public function actionConsume(string $queueName, int $messagesCount = 100)
    {
        $this->consumer->consume($queueName, $messagesCount);

        return ExitCode::OK;
    }
}
