<?php

namespace hiapi\endpoints;

use Closure;

trait EndpointProviderTrait
{
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
