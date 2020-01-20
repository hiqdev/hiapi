<?php


namespace hiapi\endpoints\Module\Middleware;

use Closure;

/**
 * Trait MiddlewareBuilderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait MiddlewareBuilderTrait
{
    /**
     * @psalm-var list<string|Closure>
     */
    protected $pipe = [];

    public function pipe(...$middlewares)
    {
        $this->pipe = $middlewares;

        return $this;
    }
}
