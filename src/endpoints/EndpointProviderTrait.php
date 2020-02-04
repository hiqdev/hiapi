<?php

namespace hiapi\endpoints;

use Closure;
use Guzzle\Common\Collection;

trait EndpointProviderTrait
{
    protected function call(string $className): \Closure
    {
        return static function ($input, callable $next) use ($className) {
            return \Yii::$container->get($className)->__invoke($input);
        };
    }

    protected function collectionOf(string $className): \Closure
    {
        return Collection::of($className);
    }

    // TODO: move to internal project
    protected function checkSelf(): Closure
    {
        return static function ($command, $next) {
            return $next($command);
        };
    }

    protected function input(array $inputOptions): array
    {
        // TODO: Take Tafid's Pact PR
        return $inputOptions;
    }
    protected function output(array $inputOptions): array
    {
        // TODO: Take Tafid's Pact PR
        return $inputOptions;
    }
}
