<?php

namespace hiapi\Core\Endpoint\Middleware;

use hiapi\Core\Endpoint\Endpoint;
use hiapi\exceptions\InsufficientPermissionsException;
use hiapi\legacy\components\User;
use League\Tactician\Middleware;

/**
 * Class CheckPermissionsMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CheckPermissionsMiddleware implements Middleware
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var string[]
     */
    private $permissions;

    public function __construct(Endpoint $endpoint, User $user)
    {
        $this->permissions = $endpoint->getPermissions();
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function execute($command, callable $next)
    {
        foreach ($this->permissions as $permission) {
            if (! $this->user->can($permission)) {
                throw new InsufficientPermissionsException($permission);
            }
        }

        return $next($command);
    }
}
