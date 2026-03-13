<?php

namespace hiapi\tests\unit\Endpoints;

use hiapi\tests\unit\Endpoints\support\FakeEndpointBuilder;
use hiapi\tests\unit\Endpoints\support\InputStub;
use hiapi\tests\unit\Endpoints\support\ReturnStub;
use PHPUnit\Framework\TestCase;

/**
 * @group endpoints
 */
class EndpointBuilderTest extends TestCase
{
    protected $builder;

    protected function setUp(): void
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
