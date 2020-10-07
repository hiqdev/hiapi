<?php
declare(strict_types=1);

namespace hiapi\Core\Http;

use hiapi\Core\Http\Psr15\Middleware\EndpointMiddleware;
use Yiisoft\Router\Route;

class EndpointRoute
{
    public static function get(string $pattern, string $command): Route
    {
        return Route::get($pattern, EndpointMiddleware::for($command));
    }
}
