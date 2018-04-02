<?php

namespace hiapi\middlewares;

use hiapi\event\EventStorageInterface;
use League\Event\EmitterInterface;
use League\Tactician\Middleware;

/**
 * Class EventEmitterMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class EventEmitterMiddleware implements Middleware
{
    /**
     * @var \hiapi\event\EventStorageInterface
     */
    private $eventStorage;
    /**
     * @var EmitterInterface
     */
    private $emitter;

    public function __construct(EventStorageInterface $eventStorage, EmitterInterface $emitter)
    {
        $this->eventStorage = $eventStorage;
        $this->emitter = $emitter;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $result = $next($command);

        $events = $this->eventStorage->release();
        if (!empty($events)) {
            $this->emitter->emitBatch($events);
        }

        return $result;
    }
}
