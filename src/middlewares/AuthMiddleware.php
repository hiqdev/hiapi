<?php


namespace hiapi\middlewares;

use hiapi\exceptions\InsufficientPermissionsException;
use hiapi\exceptions\NotAuthenticatedException;
use League\Tactician\Middleware;
use yii\web\User;

/**
 * Class AuthMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class AuthMiddleware implements Middleware
{
    public function __construct(private readonly string $permission, private readonly User $user)
    {
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     * @throws InsufficientPermissionsException
     * @throws NotAuthenticatedException
     */
    public function execute($command, callable $next)
    {
        if ($this->user->getId() === null) {
            throw new NotAuthenticatedException();
        }

        if (!$this->user->can($this->permission)) {
            throw new InsufficientPermissionsException($this->permission);
        }

        return $next($command);
    }
}
