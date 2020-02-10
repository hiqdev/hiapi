<?php

namespace hiapi\endpoints\Module\InOutControl\VO;

class Collection
{
    /**
     * @var string
     */
    private $entriesClass;

    private function __construct()
    {
    }

    /**
     * @return string
     */
    public function getEntriesClass(): string
    {
        return $this->entriesClass;
    }

    public static function of(string $className): self
    {
        $self = new self();
        $self->entriesClass= $className;
        return $self;
    }
}
