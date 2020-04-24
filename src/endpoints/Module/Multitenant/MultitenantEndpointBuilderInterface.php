<?php


namespace hiapi\endpoints\Module\Multitenant;

/**
 * Interface MultitenantEndpointBuilderInterface
 *
 * @template T of MultitenantEndpointBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface MultitenantEndpointBuilderInterface
{
    /**
     * @psalm-param Tenant::WEB|Tenant::CLI|Tenant::QUEUE|Tenant::ALL $tenantMask
     * @return T
     */
    public function exportTo(int $tenantMask);
}
