<?php

declare(strict_types=1);

namespace hiapi\tests\unit\Endpoints\support;

use Closure;
use hiapi\endpoints\EndpointBuilderInterface;
use hiapi\endpoints\EndpointConfiguration;
use hiapi\endpoints\Module\InOutControl\ExamplesAwareBuilderInterface;
use hiapi\endpoints\Module\InOutControl\ExamplesAwareBuilderTrait;
use hiapi\endpoints\Module\InOutControl\InOutControlBuilderInterface;
use hiapi\endpoints\Module\InOutControl\InOutControlBuilderTrait;

/**
 * Class TestABC
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FakeEndpointBuilder implements
    EndpointBuilderInterface,
    ExamplesAwareBuilderInterface,
    InOutControlBuilderInterface
{
    use ExamplesAwareBuilderTrait;
    use InOutControlBuilderTrait {
        InOutControlBuilderTrait::buildInOutParameters as buildInOutControl;
    }

    /**
     * @return Closure[]
     */
    protected function getBuildersList(): array
    {
        return [
            Closure::fromCallable([$this, 'buildExamples']),
            Closure::fromCallable([$this, 'buildInOutControl']),
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

        return FakeEndpoint::fromConfig($config);
    }
}
