<?php

namespace hiapi\endpoints\Module\InOutControl\VO;

use hiapi\commands\BaseCommand;

/**
 * Class Collection
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @template-covariant T of \hiapi\commands\BaseCommand
 * @psalm-immutable
 */
class Collection
{
    /**
     * @psalm-var class-string<T>
     */
    private string $entriesClass;

    /**
     * @psalm-param class-string $entriesClass
     */
    private function __construct(string $entriesClass)
    {
        $this->entriesClass = $entriesClass;
    }

    /**
     * @psalm-return class-string<T>
     */
    public function getEntriesClass(): string
    {
        return $this->entriesClass;
    }

    /**
     * @psalm-param class-string $className
     */
    public static function of(string $className): self
    {
        return new self($className);
    }
}
