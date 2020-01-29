<?php

namespace hiapi\endpoints\Module\Middleware;

use Closure;

/**
 * Interface MiddlewareBuilderInterface
 *
 * @template T of MiddlewareBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface MiddlewareBuilderInterface
{
    /**
     * @param string|Closure ...$middlewares
     * @return T
     */
    public function middlewares(...$middlewares);
}
