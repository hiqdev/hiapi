<?php

namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\commands\error\AuthenticationError;
use hiapi\commands\error\LogicalError;
use hiapi\commands\error\RuntimeError;
use hiapi\exceptions\domain\DomainException;
use hiapi\exceptions\NotAuthenticatedException;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;

/**
 * Class HandleExceptionsMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class HandleExceptionsMiddleware implements Middleware
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @var bool
     */
    protected $keepSystemErrorMessage = false;

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
        } catch (DomainException|\DomainException $domainException) {
            return $this->handleDomainException($command, $domainException);
        } catch (\InvalidArgumentException $argumentException) {
            return $this->handleArgumentException($command, $argumentException);
        } catch (\Exception $exception) {
            return $this->handleUnclassifiedError($command, $exception);
        }
    }

    public function setKeepSystemErrorMessage(bool $keepSystemErrorMessage): void
    {
        $this->keepSystemErrorMessage = $keepSystemErrorMessage;
    }

    private function handleDomainException(BaseCommand $command, \Exception $domainException)
    {
        return new LogicalError($command, $domainException);
    }

    private function handleArgumentException(BaseCommand $command, \InvalidArgumentException $exception)
    {
        return new RuntimeError($command, $this->ensureExceptionCanBeKept($exception));
    }

    private function handleUnclassifiedError(BaseCommand $command, \Exception $exception)
    {
        return new RuntimeError($command, $this->ensureExceptionCanBeKept($exception));
    }

    private function handleAuthenticationError(BaseCommand $command, NotAuthenticatedException $exception)
    {
        return new AuthenticationError($command, $exception);
    }

    private function ensureExceptionCanBeKept(\Exception $exception): \Exception
    {
        $this->logger->warning('Uncaught exception ' . \get_class($exception), ['message' => $exception->getMessage()]);

        if (!$this->keepSystemErrorMessage) {
            return new \Exception('System error', $exception->getCode(), $exception);
        }

        return $exception;
    }
}
