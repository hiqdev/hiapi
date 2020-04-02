<?php

namespace hiapi\commands\Search\Filter\Type;

class ArrayOfFilter extends AbstractFilter
{
    /**
     * @var string
     */
    private $elementsType;

    public function __construct(string $name, string $elementsType)
    {
        parent::__construct($name);
        $this->elementsType = $elementsType;
    }

    /**
     * @return string
     */
    public function getElementsType(): string
    {
        return $this->elementsType;
    }
}
