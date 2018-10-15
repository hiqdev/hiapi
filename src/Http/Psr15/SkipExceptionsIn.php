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
        $isExecutingNextHandler = false;
        $next = new class($isExecutingNextHandler, $handler) implements RequestHandlerInterface {
            /**
             * @var bool
             */
            private $isExecutingNextHandler;
            /**
             * @var RequestHandlerInterface
             */
            private $nextHandler;

            public function __construct(bool &$isExecutingNextHandler, RequestHandlerInterface $realHandler)
            {
                $this->isExecutingNextHandler = &$isExecutingNextHandler;
                $this->nextHandler = $realHandler;
            }

            /**
             * Handle the request and return a response.
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->isExecutingNextHandler = true;
                $result = $this->nextHandler->handle($request);
                $this->isExecutingNextHandler = false;

                return $result;
            }
        };

        try {
            return $this->middleware->process($request, $next);
        } catch (\Throwable $throwable) {
            if ($isExecutingNextHandler) {
                throw $throwable;
            }

            return $handler->handle($request);
        }
    }
}
