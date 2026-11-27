<?php

namespace hiapi\endpoints;

use ArrayAccess;

/**
 * Interface EndpointConfigurationInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @extends ArrayAccess<string, mixed>
 */
interface EndpointConfigurationInterface extends ArrayAccess
{
    /**
     * @param string $property
     * @param $value
     * @return $this
     */
    public function set(string $property, $value): EndpointConfigurationInterface;

    public function overwrite(string $property, $value): EndpointConfigurationInterface;

    public function unset(string $property): EndpointConfigurationInterface;
}
