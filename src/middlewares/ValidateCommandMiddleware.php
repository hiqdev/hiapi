<?php declare(strict_types=1);

namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\exceptions\domain\ValidationException;

/**
 * Class ValidateCommandMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ValidateCommandMiddleware
{
    /**
     * @param object|BaseCommand $command
     * @param callable $next
     *
     * @return mixed
     * @throws ValidationException when data is not valid
     */
    public function execute($command, callable $next)
    {
        if (!$command->validate()) {
            throw new ValidationException(implode(', ', $command->getFirstErrors()));
        }

        return $next($command);
    }
}
