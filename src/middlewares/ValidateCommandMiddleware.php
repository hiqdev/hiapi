<?php declare(strict_types=1);

namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\exceptions\domain\ValidationException;
use League\Tactician\Middleware;

/**
 * Class ValidateCommandMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ValidateCommandMiddleware implements Middleware
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
            // TODO: enhance handling of multiple errors in the same command.
            $errors = $command->getFirstErrors();

            throw new ValidationException(sprintf(
                '%s: %s',
                array_key_first($errors),
                reset($errors)
            ));
        }

        return $next($command);
    }
}
