<?php


namespace hiapi\Core\Http\Psr15\Middleware;


use hiapi\Core\Endpoint\EndpointRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

class FallbackToLegacyApiMiddleware implements MiddlewareInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $newMiddlewares;
    /**
     * @var MiddlewareInterface[]
     */
    private $legacyMiddlewares;
    /**
     * @var EndpointRepository
     */
    private $endpointRepository;

    /**
     * FallbackToLegacyApiMiddleware constructor.
     *
     * @param EndpointRepository $endpointRepository
     */
    public function __construct(EndpointRepository $endpointRepository)
    {
        $this->endpointRepository = $endpointRepository;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queue = $this->newMiddlewares;
        if (!$this->endpointRepository->has(trim($request->getUri()->getPath(), '/'))) {
            $queue = $this->legacyMiddlewares;
        }

        return (new Relay($queue))->handle($request);
    }

    /**
     * @param MiddlewareInterface[] $newMiddlewares
     * @return FallbackToLegacyApiMiddleware
     */
    public function newMiddlewares(array $newMiddlewares): FallbackToLegacyApiMiddleware
    {
        $this->newMiddlewares = $newMiddlewares;
        return $this;
    }

    /**
     * @param MiddlewareInterface[] $legacyMiddlewares
     * @return FallbackToLegacyApiMiddleware
     */
    public function legacyMiddlewares(array $legacyMiddlewares): FallbackToLegacyApiMiddleware
    {
        $this->legacyMiddlewares = $legacyMiddlewares;
        return $this;
    }
}
