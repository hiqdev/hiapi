<?php

namespace hiapi\event;

use hiapi\event\EventStorageInterface;

/**
 * Class EventStorage
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class EventStorage implements EventStorageInterface
{
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @param object ...$events
     */
    public function store(...$events): void
    {
        $this->events = array_merge($this->events, $events);
    }

    public function release(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
