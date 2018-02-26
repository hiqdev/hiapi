<?php

namespace hiapi\middlewares;

use League\Tactician\Middleware;

/**
 * Class PassthroughCommandHandler
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PassthroughCommandHandler implements Middleware
{
    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        return $command;
    }
}
