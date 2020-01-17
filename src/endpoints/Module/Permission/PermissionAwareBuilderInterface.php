<?php


namespace hiapi\endpoints\Module\Permission;

/**
 * Interface PermissionAwareBuilderInterface
 *
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface PermissionAwareBuilderInterface
{
    /**
     * @param string $permission
     */
    public function checkPermission(string $permission);
}
