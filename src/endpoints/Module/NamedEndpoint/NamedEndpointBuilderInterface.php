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
     * @param string $permission
     * @return T
     */
    public function name(string $permission);
}
