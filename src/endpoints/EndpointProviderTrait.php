<?php

namespace hiapi\endpoints;

use Closure;

trait EndpointProviderTrait
{
    protected function handler(string $className): \Closure
    {
        return function ($input, callable $next) use ($className) {
            // TODO: separate class
            xdebug_break();
            return \Yii::$container->get($className)->__invoke($input);
        };
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
