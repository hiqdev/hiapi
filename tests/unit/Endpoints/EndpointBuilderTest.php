<?php

namespace hiapi\tests\unit\Endpoints;

use Closure;
use hiapi\endpoints\EndpointBuilderInterface;
use hiapi\endpoints\EndpointConfiguration;
use hiapi\endpoints\Module\Builder\OrderedBuildersCallTrait;
use hiapi\endpoints\Module\Builder\ReflectionBasedEndpointBuilderTrait;
use hiapi\endpoints\Module\InOutControl\ExamplesAwareBuilderInterface;
use hiapi\endpoints\Module\InOutControl\ExamplesAwareBuilderTrait;
use hiapi\endpoints\Module\InOutControl\InOutControlBuilderInterface;
use hiapi\endpoints\Module\InOutControl\InOutControlBuilderTrait;
use hiapi\tests\unit\Endpoints\support\FakeEndpoint;
use hiapi\tests\unit\Endpoints\support\InputStub;
use hiapi\tests\unit\Endpoints\support\ReturnStub;
use PHPUnit\Framework\TestCase;

/**
 * @group endpoints
 */
class EndpointBuilderTest extends TestCase
{
    protected $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new FakeEndpointBuilder();
    }

    public function testBuilderCanProduceEndpoint()
    {
        $builder = $this->builder
            ->take(InputStub::class)
            ->return(ReturnStub::class);

        $endpoint = $builder->build();

        $this->assertSame(InputStub::class, $endpoint->getConfig()['take']);
        $this->assertSame(ReturnStub::class, $endpoint->getConfig()['return']);
    }
}

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
        buildInOutParameters as buildInOutControl;
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
};
