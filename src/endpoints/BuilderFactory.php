<?php

namespace hiapi\endpoints;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class BuilderFactory implements BuilderFactoryInterface
{
    public function endpoint(string $className): EndpointBuilderInterface
    {
        return (new EndpointBuilder())
            ->name($this->nameFromClassName($className));
    }

    private function nameFromClassName(string $className): string
    {
        return (new ReflectionClass($className))->getShortName();
    }
}
