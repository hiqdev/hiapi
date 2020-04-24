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
    private User $user;
    private Endpoint $endpoint;

    public function __construct(Endpoint $endpoint, User $user)
    {
        $this->endpoint = $endpoint;
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function execute($command, callable $next)
    {
        foreach ($this->endpoint->permissions as $permission) {
            if (! $this->user->can($permission)) {
                throw new InsufficientPermissionsException($permission);
            }
        }

        return $next($command);
    }
}
