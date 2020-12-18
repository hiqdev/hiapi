<?php

namespace hiapi\Core\Endpoint;

use Closure;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use hiapi\middlewares\CallableHandler;
use ReflectionClass;
use hiapi\Core\Endpoint\Middleware\ReduceHandler;
use hiapi\Core\Endpoint\Middleware\RepeatHandler;
use hiapi\endpoints\BuilderFactoryInterface;
use hiqdev\yii\compat\yii;
use League\Tactician\Middleware;

/**
 * Class BuilderFactory
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class BuilderFactory implements BuilderFactoryInterface
{
    public function endpoint(string $className): EndpointBuilder
    {
        return (new EndpointBuilder())
            ->name($this->nameFromClassName($className))
            ->definedBy($className);
    }

    private function nameFromClassName(string $className): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new ReflectionClass($className))->getShortName();
    }

    public function call(string $className)
    {
        return [
            '__class' => CallableHandler::class,
            '__construct()' => [yii::referenceTo($className)],
        ];
    }

    /**
     * @param Middleware|callable|string $handler
     * @return array
     */
    public function repeat($handler)
    {
        return [
            '__class' => RepeatHandler::class,
            '__construct()' => [$this->prepareHandler($handler)],
        ];
    }

    private function prepareHandler($handler)
    {
        return is_string($handler) ? yii::referenceTo($handler) : $handler;
    }

    /**
     * @param Middleware|callable|string $handler
     * @return array
     */
    public function reduce($handler)
    {
        return [
            '__class' => ReduceHandler::class,
            '__construct()' => [$this->prepareHandler($handler)],
        ];
    }

    public function many(string $className): Collection
    {
        return Collection::of($className);
    }

    // TODO: move to internal project
    public function checkSelf(): Closure
    {
        return static function ($command, $next) {
            return $next($command);
        };
    }

    public function input(array $inputOptions): array
    {
        // TODO: Take Tafid's Pact PR
        return $inputOptions;
    }
    public function output(array $inputOptions): array
    {
        // TODO: Take Tafid's Pact PR
        return $inputOptions;
    }
}
