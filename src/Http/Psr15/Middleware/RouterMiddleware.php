<?php
declare(strict_types=1);

namespace hiapi\Core\Http\Psr15\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;

/**
 * Performs matched route.
 * Passes pattern matched parameters into request query parameters.
 * If no route matched then proceeds.
 */
class RouterMiddleware implements MiddlewareInterface
{
    private array $routes;
    private MiddlewareDispatcher $dispatcher;

    public function __construct(array $routes = [], MiddlewareDispatcher $dispatcher)
    {
        $this->routes = $routes;
        $this->dispatcher = $dispatcher;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $group = Group::create(null, $this->dispatcher)->routes(...$this->routes);
        $matcher = new UrlMatcher(new RouteCollection($group));

        $result = $matcher->match($request);
        if ($result->parameters()) {
            $query = array_merge($result->parameters(), $request->getQueryParams());
            $request = $request->withQueryParams($query);
        }

        return $result->process($request, $handler);
    }
}
