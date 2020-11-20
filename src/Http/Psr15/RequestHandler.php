<?php

declare(strict_types=1);

namespace hiapi\Core\Http\Psr15;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares;

    public function __construct(... $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $relay = new Relay($this->middlewares);
        $response = $relay->handle($request);

        return $response;
    }
}
