<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use hiqdev\yii\compat\Injector;

class IfMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly Closure $if, private readonly MiddlewareInterface $then, private readonly MiddlewareInterface $else, private readonly Injector $injector)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $if = $this->injector->invoke($this->if, ['request' => $request]);
        $middleware = $if ? $this->then : $this->else;

        return $middleware->process($request, $handler);
    }
}
