<?php

namespace hiapi\Http\Psr15;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class SkipExceptionsIn
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SkipExceptionsIn implements MiddlewareInterface
{
    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    public function __construct(MiddlewareInterface $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->middleware->process($request, $handler);
        } catch (\Throwable $throwable) {
            return $handler->handle($request);
        }
    }
}
