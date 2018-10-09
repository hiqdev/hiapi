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
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
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
