<?php

namespace hiapi\event\listener;

use hiqdev\yii\compat\yii;
use JsonSerializable;
use League\Event\EventInterface;
use League\Event\ListenerInterface;
use Psr\Log\LoggerInterface;
use yii\helpers\FileHelper;

/**
 * Class LogEventsListener
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class LogEventsListener implements ListenerInterface
{
    private LoggerInterface $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $eventString = $this->serializeEvent($event);
        if ($eventString === null) {
            $this->log->warning('Do not know how to serialize events that does not implement \JsonSerializable interface.');
            return;
        }

        $dir = yii::getAlias('@runtime');

        FileHelper::createDirectory($dir);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'events.log', $eventString . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Check whether the listener is the given parameter.
     *
     * @param mixed $listener
     *
     * @return bool
     */
    public function isListener($listener)
    {
        return true;
    }

    private function serializeEvent(EventInterface $event): ?string
    {
        if ($event instanceof JsonSerializable) {
            return json_encode($event->jsonSerialize());
        }

        return null;
    }
}
