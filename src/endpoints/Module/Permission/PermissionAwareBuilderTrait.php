<?php


namespace hiapi\endpoints\Module\Permission;

trait PermissionAwareBuilderTrait
{
    /**
     * @var string
     */
    protected $checkPermission = null;

    public function checkPermission(string $permission)
    {
        $this->checkPermission = $permission;

        return $this;
    }
}
