<?php

namespace hiapi\endpoints;

use hiapi\endpoints\Exception\EndpointBuildingException;

class EndpointBuilder implements EndpointBuilderInterface
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected $exportTo;
    /**
     * @var string
     */
    protected $inputClassName;
    /**
     * @var string
     */
    protected $checkPermission;
    /**
     * @var array|\Closure[]|string[]
     */
    protected $middlewares = [];
    /**
     * @var string
     */
    protected $resultClassName;

    public function name(string $name): EndpointBuilderInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function exportTo(int $direction): EndpointBuilderInterface
    {
        $this->exportTo = $direction;

        return $this;
    }

    public function take(string $className): EndpointBuilderInterface
    {
        $this->inputClassName = $className;

        return $this;
    }

    public function checkPermission(string $permission): EndpointBuilderInterface
    {
        $this->checkPermission = $permission;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function call(...$middlewares): EndpointBuilderInterface
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function return(string $className): EndpointBuilderInterface
    {
        $this->resultClassName = $className;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withExamples(array $examples): EndpointBuilderInterface
    {
        return $this;
    }

    /**
     * @throws EndpointBuildingException
     * @return Endpoint
     */
    public function build()
    {
        if ($this->name === null) {
            throw new EndpointBuildingException('Command name must be set');
        }

        $endpoint = new Endpoint($this->name);
        if ($this->inputClassName) {
            $endpoint->inputClassName = $this->inputClassName;
        }
        if ($this->resultClassName) {
            $endpoint->resultClassName = $this->resultClassName;
        }
        if (count($this->middlewares)) {
            $endpoint->middlewares = $this->middlewares;
        }


    }
}
