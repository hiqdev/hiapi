<?php

namespace hiapi\tests\unit\Endpoints\support;

class FakeEndpoint
{
    public static function fromConfig(\hiapi\endpoints\EndpointConfig $config)
    {
        return new self();
    }
}
