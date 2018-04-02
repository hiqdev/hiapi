<?php

namespace hiapi\event\listeners;

use League\Event\EventInterface;
use League\Event\ListenerInterface;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class LogEventsListener
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class LogEventsListener implements ListenerInterface
{
    public function __construct()
    {
        $a = 0;
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
        file_put_contents(Yii::getAlias('@runtime/events.log'), $this->serializeEvent($event), FILE_APPEND);
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

    private function serializeEvent(EventInterface $event)
    {
        if ($event instanceof \JsonSerializable) {
            return json_encode($event->jsonSerialize());
        }

        throw new InvalidConfigException('Do not know how to serialize events that does not implement \JsonSerializable interface.');
    }
}
