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
    /**
     * @var Closure
     */
    private $if;
    /**
     * @var MiddlewareInterface
     */
    private $then;
    /**
     * @var MiddlewareInterface
     */
    private $else;
    /**
     * @var Injector
     */
    private $injector;

    public function __construct(Closure $if, MiddlewareInterface $then, MiddlewareInterface $else, Injector $injector)
    {
        $this->if = $if;
        $this->then = $then;
        $this->else = $else;
        $this->injector = $injector;
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
