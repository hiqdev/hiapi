<?php


namespace hiapi\Core\Endpoint;

use hiapi\exceptions\ConfigurationException;
use Psr\Container\ContainerInterface;

class EndpointRepository
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var BuilderFactory
     */
    private $builderFactory;

    private $endpoints = [];

    public function __construct(array $endpoints, ContainerInterface $container, BuilderFactory $builderFactory)
    {
        foreach ($endpoints as $name => $handler) {
            $this->addEndpoint($name, $handler);
        }
        $this->container = $container;
        $this->builderFactory = $builderFactory;
    }

    public function has(string $name): bool
    {
        return isset($this->getEndpoints()[$name]);
    }

    public function addEndpoint(string $name, string $handlerClassName): self
    {
        $this->endpoints[$name] = $handlerClassName;

        return $this;
    }

    public function getByName(string $name): Endpoint
    {
        if (!$this->has($name)) {
            throw new ConfigurationException(sprintf('Endpoint %s does not exist', $name));
        }

        return $this->container->get($this->getEndpoints()[$name])($this->builderFactory);
    }

    private function getEndpoints(): iterable
    {
        return $this->endpoints;
    }
}
