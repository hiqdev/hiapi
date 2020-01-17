<?php

namespace hiapi\endpoints;

interface EndpointBuilderInterface
{
    /**
     * The command name
     *
     * @param string $name
     * @return $this
     */
    public function name(string $name): self;

    /**
     * @param int $direction
     * @return $this
     */
    public function exportTo(int $direction): self;
    public function take(string $className): self;
    public function checkPermission(string $permission): self;

    /**
     * @param string|\Closure ...$middlewares
     * @return $this
     */
    public function call(...$middlewares): self;

    public function return(string $className): self;

    /**
     * @param array $examples
     * @return $this
     */
    public function withExamples(array $examples): self;
}
