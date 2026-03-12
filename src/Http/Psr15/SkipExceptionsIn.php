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
    public function __construct(private readonly MiddlewareInterface $middleware)
    {
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

            public function __construct(bool &$isExecutingNextHandler, private readonly RequestHandlerInterface $nextHandler)
            {
                $this->isExecutingNextHandler = &$isExecutingNextHandler;
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
