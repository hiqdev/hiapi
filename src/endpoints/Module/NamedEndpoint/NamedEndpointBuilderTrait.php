<?php


namespace hiapi\endpoints\Module\NamedEndpoint;

trait NamedEndpointBuilderTrait
{
    /**
     * @var string
     */
    protected $name = null;

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
