<?php
declare(strict_types=1);

namespace hiapi\Core\Http\Psr15\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Router\RouteCollector;

/**
 * Performs matched route.
 * Passes pattern matched parameters into request query parameters.
 * If no route matched then proceeds.
 */
class RouterMiddleware implements MiddlewareInterface
{
    private MiddlewareDispatcher $dispatcher;
    private array $routes;

    public function __construct(MiddlewareDispatcher $dispatcher, array $routes = [])
    {
        $this->dispatcher = $dispatcher;
        $this->routes = $routes;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $group = Group::create(null, $this->dispatcher)->routes(...$this->routes);
        $collector = (new RouteCollector())->addGroup($group);
        $matcher = new UrlMatcher(
            new RouteCollection($collector),
            new CurrentRoute()
        );

        $result = $matcher->match($request);
        if ($result->parameters()) {
            $query = array_merge($result->parameters(), $request->getQueryParams());
            $request = $request->withQueryParams($query);
        }

        return $result->process($request, $handler);
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
