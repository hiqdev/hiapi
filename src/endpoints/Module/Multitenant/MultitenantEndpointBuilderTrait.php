<?php


namespace hiapi\endpoints\Module\Multitenant;

use hiapi\endpoints\EndpointConfigurationInterface;

/**
 * Trait MultitenantEndpointBuilderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait MultitenantEndpointBuilderTrait
{
    protected int $tenantMask;

    /** {@inheritDoc} */
    public function exportTo(int $tenantMask)
    {
        $this->tenantMask = $tenantMask;

        return $this;
    }

    protected function buildTenants(EndpointConfigurationInterface $configuration): void
    {
        $configuration->set('tenantMask', $this->tenantMask);
    }
}
