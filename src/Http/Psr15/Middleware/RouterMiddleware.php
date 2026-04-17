<?php declare(strict_types=1);

namespace hiapi\Core\Http\Psr15\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Router\RouteCollector;

/**
 * Performs matched route.
 * Passes pattern-matched parameters into request query parameters.
 * If no route is matched, then proceeds.
 */
readonly class RouterMiddleware implements MiddlewareInterface
{
    public function __construct(private MiddlewareDispatcher $dispatcher, private array $routes = [])
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $group = Group::create()->routes(...$this->routes);
        $collector = new RouteCollector()->addRoute($group);
        $matcher = new UrlMatcher(new RouteCollection($collector));

        $result = $matcher->match($request);
        if ($result->isSuccess() && $result->arguments()) {
            $query = array_merge($result->arguments(), $request->getQueryParams());
            $request = $request->withQueryParams($query);
        }

        return $handler->handle($request);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
