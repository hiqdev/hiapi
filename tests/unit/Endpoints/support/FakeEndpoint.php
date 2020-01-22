<?php

namespace hiapi\tests\unit\Endpoints\support;

use hiapi\endpoints\EndpointConfigurationInterface;

class FakeEndpoint
{
    /**
     * @var \ArrayAccess
     */
    private $config;

    public static function fromConfig(EndpointConfigurationInterface $config)
    {
        $self = new self();
        $self->config = $config;
        return $self;
    }

    public function getConfig(): \ArrayAccess
    {
        return $this->config;
    }
}
