<?php

namespace hiapi\middlewares;

use hiapi\event\EventStorageInterface;
use League\Event\EmitterInterface;
use League\Event\EventInterface;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;

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
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EventStorageInterface $eventStorage, EmitterInterface $emitter, LoggerInterface $logger)
    {
        $this->eventStorage = $eventStorage;
        $this->emitter = $emitter;
        $this->logger = $logger;
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
        $this->emitEvents($events);

        return $result;
    }

    /**
     * @param EventInterface[] $events
     */
    private function emitEvents(array $events = []): void
    {
        foreach ($events as $event) {
            try {
                $this->emitter->emit($event);
            } catch (\Exception $exception) {
                $this->logger->error("Failed to handle event {$event->getName()}: {$exception->getMessage()}", [
                    'exception' => $exception,
                ]);
            }
        }
    }
}
