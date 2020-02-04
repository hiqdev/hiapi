<?php


namespace hiapi\middlewares;

use League\Tactician\Middleware;

/**
 * Class CallableHandler
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CallableHandler implements Middleware
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        return call_user_func($this->callable, $command, $next);
    }
}
