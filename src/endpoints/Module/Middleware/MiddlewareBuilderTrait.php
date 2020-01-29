<?php


namespace hiapi\endpoints\Module\Middleware;

use hiapi\endpoints\EndpointConfigurationInterface;

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
    protected $middlewares = [];

    public function middlewares(...$middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    protected function buildMiddlewares(EndpointConfigurationInterface $configuration)
    {
        $configuration->set('middlewares', $this->middlewares);
    }
}
