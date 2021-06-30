<?php

namespace hiapi\Core\Endpoint;

use hiapi\endpoints\EndpointConfigurationInterface;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use hiapi\endpoints\Module\Multitenant\Tenant;
use Webmozart\Assert\Assert;
use hiapi\commands\BaseCommand;

/**
 * Class Endpoint
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-immutable
 */
final class Endpoint
{
    public string $name;
    public ?string $description;

    /** @psalm-var ?class-string */
    public ?string $definedBy;
    /** @psalm-var Tenant::CLI|Tenant::WEB */
    public int $tenantMask;
    /** @psalm-var Collection|class-string<BaseCommand> */
    public $inputType;
    /** @var \Closure[]|callable[] */
    public array $middlewares = [];
    /** @psalm-var list<string> */
    public array $permissions = [];
    /** @psalm-var Collection|class-string */
    public $returnType;
    /** @var bool */
    public $returnIsNullable = false;

    /**
     * // TODO
     */
    public $examples;

    public static function fromConfig(EndpointConfigurationInterface $config): self
    {
        $self = new self();

        Assert::notEmpty($config['name'], 'Endpoint MUST have a name');
        $self->name = $config['name'];
        $self->definedBy = $config['definitionClassName'] ?? null;
        $self->permissions = !empty($config['permission']) ? [$config['permission']] : [];
        $self->tenantMask = $config['tenantMask'] ?? Tenant::CLI;

        Assert::notEmpty($config['inputType'], 'Endpoint input definition is required');
        $self->inputType = $config['inputType'];

        Assert::notEmpty($config['returnType'], 'Endpoint return definition is required');
        $self->returnType = $config['returnType'];
        $self->returnIsNullable = (bool)$config['returnIsNullable'];

        Assert::isArray($config['middlewares'] ?? []);
        $self->middlewares = $config['middlewares'] ?? [];

        $self->examples = $config['examples'] ?? null;
        $self->description = $config['description'] ?? null;

        return $self;
    }

    private function __construct()
    {
    }
}
