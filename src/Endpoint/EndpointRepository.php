<?php


namespace hiapi\Core\Endpoint;

use hiapi\exceptions\ConfigurationException;
use Psalm\Type\Atomic\TLiteralClassString;
use Psr\Container\ContainerInterface;

class EndpointRepository
{
    private ContainerInterface $container;

    private BuilderFactory $builderFactory;

    /**
     * @var array<string, string|TLiteralClassString>
     */
    private array $endpoints = [];

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
        return isset($this->endpoints[$name]);
    }

    /**
     * @param string $name
     * @param string $handlerClassName
     * @psalm-param TLiteralClassString $handlerClassName
     * @return $this
     */
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

        return $this->container->get($this->endpoints[$name])($this->builderFactory);
    }

    /**
     * @psalm-return list<string>
     */
    public function getEndpointNames(): array
    {
        return array_keys($this->endpoints);
    }
}
