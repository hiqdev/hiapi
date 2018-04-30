<?php

namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\commands\error\AuthenticationError;
use hiapi\commands\error\LogicalError;
use hiapi\commands\error\CommandError;
use hiapi\commands\error\RuntimeError;
use hiapi\exceptions\domain\DomainException;
use hiapi\exceptions\NotAuthenticatedException;
use League\Tactician\Middleware;
use yii\web\HttpException;

/**
 * Class HandleExceptionsMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class HandleExceptionsMiddleware implements Middleware
{
    /**
     * @param object|BaseCommand $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        try {
            return $next($command);
        } catch (NotAuthenticatedException $exception) {
            return $this->handleAuthenticationError($command, $exception);
        } catch (DomainException $domainException) {
            return $this->handleDomainException($command, $domainException);
        } catch (\InvalidArgumentException $argumentException) {
            return $this->handleArgumentException($command, $argumentException);
        } catch (\Exception $exception) {
            return $this->handleUnclassifiedError($command, $exception);
        }
    }

    private function handleDomainException(BaseCommand $command, DomainException $domainException)
    {
        return new LogicalError($command, $domainException);
    }

    private function handleArgumentException(BaseCommand $command, \InvalidArgumentException $argumentException)
    {
        return new LogicalError($command, $argumentException);
    }

    private function handleUnclassifiedError(BaseCommand $command, \Exception $exception)
    {
        return new RuntimeError($command, $exception);
    }

    private function handleAuthenticationError(BaseCommand $command, NotAuthenticatedException $exception)
    {
        return new AuthenticationError($command, $exception);
    }
}
