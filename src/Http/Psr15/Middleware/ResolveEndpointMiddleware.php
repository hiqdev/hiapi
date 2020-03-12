<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\Core\Endpoint\EndpointRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ResolveEndpointMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ResolveEndpointMiddleware implements MiddlewareInterface
{
    /**
     * @var EndpointRepository
     */
    private $endpointRepository;

    public function __construct(EndpointRepository $endpointRepository)
    {
        $this->endpointRepository = $endpointRepository;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $endpoint = $this->endpointRepository->getByName(trim($request->getUri()->getPath(), '/'));

        return $handler->handle(
            $request->withAttribute(self::class, $endpoint)
        );
    }
}
