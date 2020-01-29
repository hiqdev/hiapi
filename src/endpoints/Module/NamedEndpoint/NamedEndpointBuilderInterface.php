<?php


namespace hiapi\endpoints\Module\NamedEndpoint;

/**
 * Interface NamedEndpointBuilderInterface
 *
 * @template T of NamedEndpointBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface NamedEndpointBuilderInterface
{
    /**
     * @param string $name
     * @return T
     */
    public function name(string $name);

    /**
     * @param string $className
     * @return T
     */
    public function definedBy(string $className);
}
