<?php


namespace hiapi\endpoints\Module\NamedEndpoint;

use hiapi\endpoints\EndpointConfigurationInterface;

trait NamedEndpointBuilderTrait
{
    /**
     * @var string
     */
    protected $name;

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    protected function buildName(EndpointConfigurationInterface $configuration)
    {
        $configuration->set('name', $this->name);
    }
}
