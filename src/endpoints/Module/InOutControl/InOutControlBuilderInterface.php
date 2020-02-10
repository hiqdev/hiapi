<?php


namespace hiapi\endpoints\Module\InOutControl;

/**
 * Interface InOutControlBuilderInterface
 *
 * @template T of InOutControlBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface InOutControlBuilderInterface
{
    /**
     * @psalm-param class-string|object $classNameOrObject
     * @param string|object $classNameOrObject
     * @return T
     */
    public function take($classNameOrObject);

    /**
     * @psalm-param class-string|object $classNameOrObject
     * @param string|object $classNameOrObject
     * @return T
     */
    public function return($classNameOrObject);
}
