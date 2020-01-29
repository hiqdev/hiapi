<?php


namespace hiapi\endpoints\Module\Permission;

/**
 * Interface PermissionAwareBuilderInterface
 *
 * @template T of PermissionAwareBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface PermissionAwareBuilderInterface
{
    /**
     * @param string $permission
     * @return T
     */
    public function checkPermission(string $permission);
}
