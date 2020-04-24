<?php

namespace hiapi\Core\Endpoint;

use Closure;
use hiapi\endpoints\EndpointBuilderInterface;
use hiapi\endpoints\EndpointConfiguration;
use hiapi\endpoints\Module\Description\DescriptionBuilderInterface;
use hiapi\endpoints\Module\Description\DescriptionBuilderTrait;
use hiapi\endpoints\Module\InOutControl\ExamplesAwareBuilderInterface;
use hiapi\endpoints\Module\InOutControl\ExamplesAwareBuilderTrait;
use hiapi\endpoints\Module\InOutControl\InOutControlBuilderInterface;
use hiapi\endpoints\Module\InOutControl\InOutControlBuilderTrait;
use hiapi\endpoints\Module\Middleware\MiddlewareBuilderInterface;
use hiapi\endpoints\Module\Middleware\MiddlewareBuilderTrait;
use hiapi\endpoints\Module\Multitenant\MultitenantEndpointBuilderInterface;
use hiapi\endpoints\Module\Multitenant\MultitenantEndpointBuilderTrait;
use hiapi\endpoints\Module\NamedEndpoint\NamedEndpointBuilderInterface;
use hiapi\endpoints\Module\NamedEndpoint\NamedEndpointBuilderTrait;
use hiapi\endpoints\Module\Permission\PermissionAwareBuilderInterface;
use hiapi\endpoints\Module\Permission\PermissionAwareBuilderTrait;

/**
 * Class EndpointBuilder
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @implements PermissionAwareBuilderInterface<EndpointBuilder>
 * @implements NamedEndpointBuilderInterface<EndpointBuilder>
 * @implements MultitenantEndpointBuilderInterface<EndpointBuilder>
 * @implements InOutControlBuilderInterface<EndpointBuilder>
 * @implements MiddlewareBuilderInterface<EndpointBuilder>
 * @implements ExamplesAwareBuilderInterface<EndpointBuilder>
 */
class EndpointBuilder implements
    EndpointBuilderInterface,
    PermissionAwareBuilderInterface,
    NamedEndpointBuilderInterface,
    MultitenantEndpointBuilderInterface,
    InOutControlBuilderInterface,
    MiddlewareBuilderInterface,
    ExamplesAwareBuilderInterface,
    DescriptionBuilderInterface
{
    use PermissionAwareBuilderTrait;
    use NamedEndpointBuilderTrait;
    use MultitenantEndpointBuilderTrait;
    use InOutControlBuilderTrait;
    use MiddlewareBuilderTrait;
    use ExamplesAwareBuilderTrait;
    use DescriptionBuilderTrait;

    /**
     * @return Closure[]
     */
    protected function getBuildersList(): array
    {
        return [
            Closure::fromCallable([$this, 'buildPermissionsCheck']),
            Closure::fromCallable([$this, 'buildName']),
            Closure::fromCallable([$this, 'buildInOutParameters']),
            Closure::fromCallable([$this, 'buildTenants']),
            Closure::fromCallable([$this, 'buildMiddlewares']),
            Closure::fromCallable([$this, 'buildExamples']),
            Closure::fromCallable([$this, 'buildDescription']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function build()
    {
        $builders = $this->getBuildersList();

        $config = new EndpointConfiguration();
        foreach ($builders as $builder) {
            $builder($config);
        }

        return Endpoint::fromConfig($config);
    }
}
