<?php

namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\commands\LogicalError;
use hiapi\commands\RuntimeError;
use hiapi\exceptions\domain\DomainException;
use League\Tactician\Middleware;
use Zend\Hydrator\ExtractionInterface;

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
}
