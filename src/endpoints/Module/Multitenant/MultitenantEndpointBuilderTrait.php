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
    /**
     * @var string
     */
    protected $exportTo;

    /** {@inheritDoc} */
    public function exportTo(string $tenants)
    {
        $this->exportTo = $tenants;

        return $this;
    }

    protected function buildTenants(EndpointConfigurationInterface $configuration): void
    {
        $configuration->set('tenants', $this->exportTo);
    }
}
