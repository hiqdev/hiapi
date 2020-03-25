<?php


namespace hiapi\Core\Http\Psr15\Middleware;


use hiapi\Core\Endpoint\EndpointRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

class RelayMiddleware implements MiddlewareInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * FallbackToLegacyApiMiddleware constructor.
     *
     * @param EndpointRepository $endpointRepository
     */
    public function __construct(... $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return (new Relay($this->middlewares))->handle($request);
    }
}
