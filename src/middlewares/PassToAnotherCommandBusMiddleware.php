<?php

namespace hiapi\middlewares;

use League\Tactician\CommandBus;
use League\Tactician\Middleware;

/**
 * Class PassToAnotherCommandBusMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PassToAnotherCommandBusMiddleware implements Middleware
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        return $next($this->commandBus->handle($command));
    }
}
