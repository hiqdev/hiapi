<?php

namespace hiapi\Core\Endpoint;

use hiapi\endpoints\EndpointConfigurationInterface;
use hiapi\endpoints\Module\Multitenant\Tenant;
use Webmozart\Assert\Assert;

final class Endpoint
{
    /** @var string */
    private $name;
    /** @var int|null */
    private $tenantMask;
    /** @var \Closure[]|callable[] */
    private $middlewares;
    /**
     * // TODO: think
     */
    private $examples;
    private $permission;
    private $inputType;
    private $returnType;
    /**
     * @var string|null
     */
    private $definedBy;

    public static function fromConfig(EndpointConfigurationInterface $config)
    {
        $self = new self();

        Assert::notEmpty($config['name'], 'Endpoint MUST have a name');
        $self->name = $config['name'];
        $self->definedBy = $config['definitionClassName'] ?? null;
        $self->permission = $config['permission'] ?? null;
        $self->tenantMask = $config['tenantMask'] ?? 0x0;

        Assert::notEmpty($config['inputType'], 'Endpoint input definition is required');
        $self->inputType = $config['inputType'];

        Assert::notEmpty($config['returnType'], 'Endpoint return definition is required');
        $self->returnType = $config['returnType'];

        Assert::isArray($config['middlewares'] ?? []);
        $self->middlewares = $config['middlewares'] ?? [];

        $self->examples = $config['examples'] ?? null;

        return $self;
    }

    public function getTenantMask(): int
    {
        return $this->tenantMask ?? Tenant::CLI;
    }

    /**
     * @return string[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return array
     */
    public function getExamples(): array
    {
        return $this->examples;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return array_filter([$this->permission]);
    }

    /**
     * @return string|null
     */
    public function getDefinedBy(): ?string
    {
        return $this->definedBy;
    }

    /**
     * @return mixed
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @return mixed
     */
    public function getInputType()
    {
        return $this->inputType;
    }
}
