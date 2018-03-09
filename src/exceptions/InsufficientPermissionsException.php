<?php


namespace hiapi\exceptions;

use hiapi\exceptions\domain\DomainException;

/**
 * Class InsufficientPermissionsException
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class InsufficientPermissionsException extends DomainException
{
    /**
     * @var string
     */
    private $permission;

    /**
     * InsufficientPermissionsException constructor.
     *
     * @param string $permission
     */
    public function __construct(string $permission)
    {
        parent::__construct("Insufficient permissions for '$permission'");

        $this->permission = $permission;
    }

    /**
     * @return string
     */
    public function getFailedPermission(): string
    {
        return $this->permission;
    }
}
