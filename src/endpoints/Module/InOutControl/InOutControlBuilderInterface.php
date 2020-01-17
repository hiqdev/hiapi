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
     * @psalm-param class-string $className
     * @return T
     */
    public function take(string $className);

    /**
     * @psalm-param class-string $className
     * @return T
     */
    public function return(string $className);
}
