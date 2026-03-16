<?php

namespace hiapi\Core\Endpoint\Middleware;

use hiapi\Core\Endpoint\Endpoint;
use hiapi\exceptions\InsufficientPermissionsException;
use yii\web\User;
use League\Tactician\Middleware;

/**
 * Class CheckPermissionsMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CheckPermissionsMiddleware implements Middleware
{
    public function __construct(private readonly Endpoint $endpoint, private readonly User $user)
    {
    }

    /**
     * @inheritDoc
     */
    public function execute($command, callable $next)
    {
        foreach ($this->endpoint->permissions as $permission) {
            if (! $this->user->can($permission)
                && !$this->user->identity instanceof PrivilegedIdentity
            ) {
                throw new InsufficientPermissionsException($permission);
            }
        }

        return $next($command);
    }
}
