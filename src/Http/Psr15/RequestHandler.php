<?php

namespace hiapi\Core\Http\Psr15;

use hiapi\Core\Endpoint\EndpointProcessor;
use hiapi\Core\Endpoint\EndpointRepository;
use hiapi\Core\Http\Psr15\Middleware\AuthMiddleware;
use hiapi\Core\Http\Psr15\Middleware\BlacklistMiddleware;
use hiapi\Core\Http\Psr15\Middleware\ClientIpMiddleware;
use hiapi\Core\Http\Psr15\Middleware\CommandForEndpointMiddleware;
use hiapi\Core\Http\Psr15\Middleware\CorsMiddleware;
use hiapi\Core\Http\Psr15\Middleware\ExceptionMiddleware;
use hiapi\Core\Http\Psr15\Middleware\FallbackToLegacyApiMiddleware;
use hiapi\Core\Http\Psr15\Middleware\QuietMiddleware;
use hiapi\Core\Http\Psr15\Middleware\ResolveEndpointMiddleware;
use hiapi\Core\Http\Psr15\Middleware\RunEndpointBusMiddleware;
use hiapi\Core\Http\Psr15\Middleware\UseBaseMiddleware;
use Lcobucci\ContentNegotiation\ContentTypeMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $di;

    public function __construct(ContainerInterface $container)
    {
        $this->di = $container;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $httpMiddlewares = [];
        $httpMiddlewares[] = $this->di->get(QuietMiddleware::class);
        $httpMiddlewares[] = $this->di->get(ContentTypeMiddleware::class);
        $httpMiddlewares[] = $this->di->get(ExceptionMiddleware::class);
        $httpMiddlewares[] = $this->di->get(BlacklistMiddleware::class);
        $httpMiddlewares[] = $this->di->get(ClientIpMiddleware::class);
        $httpMiddlewares[] = $this->di->get(AuthMiddleware::class);
        $httpMiddlewares[] = $this->di->get(CorsMiddleware::class);

        $httpMiddlewares[] = $this->di->get(FallbackToLegacyApiMiddleware::class)
            ->newMiddlewares([
                $this->di->get(ResolveEndpointMiddleware::class),
                $this->di->get(CommandForEndpointMiddleware::class),
                $this->di->get(RunEndpointBusMiddleware::class),
            ])
            ->legacyMiddlewares([
                $this->di->get(UseBaseMiddleware::class),
            ]);

        $relay = new Relay($httpMiddlewares);
        $response = $relay->handle($request);

        return $response;
    }
}
