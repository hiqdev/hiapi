<?php

namespace hiapi\commands\Search\Filter\Type;

class AbstractFilter implements FilterInterface
{
    /**
     * @var string
     * @psalm-immutable
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
