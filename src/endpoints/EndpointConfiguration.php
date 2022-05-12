<?php

namespace hiapi\endpoints;

use hiapi\endpoints\Exception\EndpointBuildingException;

class EndpointConfiguration implements \ArrayAccess, EndpointConfigurationInterface
{
    private $config = [];

    public function set(string $property, $value): EndpointConfigurationInterface
    {
        if (array_key_exists($property, $this->config)) {
            throw new EndpointBuildingException(sprintf(
                'Property "%s" is already set, use $config->overwrite() if you want to change it.',
                $property
            ));
        }

        $this->config[$property] = $value;

        return $this;
    }

    public function overwrite(string $property, $value): EndpointConfigurationInterface
    {
        $this->unset($property);

        return $this->set($property, $value);
    }

    public function unset(string $property): EndpointConfigurationInterface
    {
        unset($this->config[$property]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->config[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }
}
