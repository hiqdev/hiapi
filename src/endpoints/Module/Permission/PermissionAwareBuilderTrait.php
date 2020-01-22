<?php

namespace hiapi\endpoints\Module\Permission;

use hiapi\endpoints\EndpointConfigurationInterface;

trait PermissionAwareBuilderTrait
{
    /**
     * @var string
     */
    protected $checkPermission;

    public function checkPermission(string $permission)
    {
        $this->checkPermission = $permission;

        return $this;
    }

    protected function buildPermissionsCheck(EndpointConfigurationInterface $configuration): void
    {
        $configuration->set('permission', $this->checkPermission);
    }
}
