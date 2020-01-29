<?php


namespace hiapi\endpoints\Module\NamedEndpoint;

use hiapi\endpoints\EndpointConfigurationInterface;

trait NamedEndpointBuilderTrait
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $definitionClassName;

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function definedBy(string $className)
    {
        $this->definitionClassName = $className;

        return $this;
    }

    protected function buildName(EndpointConfigurationInterface $configuration)
    {
        $configuration->set('name', $this->name);
        $configuration->set('definitionClassName', $this->definitionClassName);
    }
}
