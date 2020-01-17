<?php


namespace hiapi\endpoints\Module\Multitenant;

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
}
