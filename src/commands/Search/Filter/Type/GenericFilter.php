<?php

namespace hiapi\commands\Search\Filter\Type;

/**
 * Class GenericFilter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @method static integer(string $name): self
 *
 * @template T as self::INTEGER|self::DATETIME|self::REF
 */
class GenericFilter extends AbstractFilter
{
    public const INTEGER = self::class . '::INTEGER';
    public const DATETIME = self::class . '::DATETIME';
    public const REF =  self::class . '::REF';

    /** @psalm-var T */
    private $type;

    /**
     * GenericFilter constructor.
     *
     * @param string $name
     * @param string $type
     * @psalm-param T $type
     */
    private function __construct(string $name, string $type)
    {
        parent::__construct($name);

        $this->type = $type;
    }

    /**
     * @psaln-param T $type
     * @param string $type
     * @param array $arguments
     */
    public static function __callStatic(string $type, array $arguments): self
    {
        return new self($type, $arguments[0]);
    }

    /**
     * @return string
     * @psalm-return T
     */
    public function type(): string
    {
        return $this->type;
    }
}
