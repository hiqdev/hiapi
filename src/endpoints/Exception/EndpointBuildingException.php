<?php

namespace hiapi\endpoints\Exception;

use hiapi\endpoints\EndpointBuilderInterface;
use hiapi\exceptions\ConfigurationException;

class EndpointBuildingException extends ConfigurationException
{
    /**
     * @var EndpointBuilderInterface|null
     */
    protected $builder;

    public static function fromBuilder(string $message, EndpointBuilderInterface $builder)
    {
        $self = new static($message);
        $self->builder = $builder;

        return $self;
    }

    public function getBuilder(): ?EndpointBuilderInterface
    {
        return $this->builder;
    }
}
