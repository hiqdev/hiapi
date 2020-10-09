<?php
declare(strict_types=1);

namespace hiapi\Core\Http;

use hiapi\Core\Http\Psr15\Middleware\EndpointMiddleware;
use Yiisoft\Router\Route;

class EndpointRoute
{
    public static function get(string $pattern, string $name): Route
    {
        return Route::get($pattern, static::buildMiddlewareFor($name));
    }

    public static function post(string $pattern, string $name): Route
    {
        return Route::post($pattern, static::buildMiddlewareFor($name));
    }

    public static function put(string $pattern, string $name): Route
    {
        return Route::put($pattern, static::buildMiddlewareFor($name));
    }

    public static function delete(string $pattern, string $name): Route
    {
        return Route::delete($pattern, static::buildMiddlewareFor($name));
    }

    public static function patch(string $pattern, string $name): Route
    {
        return Route::patch($pattern, static::buildMiddlewareFor($name));
    }

    public static function head(string $pattern, string $name): Route
    {
        return Route::head($pattern, static::buildMiddlewareFor($name));
    }

    public static function options(string $pattern, string $name): Route
    {
        return Route::options($pattern, static::buildMiddlewareFor($name));
    }

    public static function anyMethod(string $pattern, string $name): Route
    {
        return Route::anyMethod($pattern, static::buildMiddlewareFor($name));
    }

    public static function buildMiddlewareFor(string $name): \Closure
    {
        return EndpointMiddleware::for($name);
    }
}
