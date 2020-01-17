<?php


namespace hiapi\middlewares;

use League\Tactician\CommandBus;
use League\Tactician\Middleware;
use yii\base\InvalidConfigException;
use yii\di\Container;

/**
 * Class PerCommandMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PerCommandMiddleware implements Middleware
{
    /**
     * @var array
     */
    public $commandMiddlewares; // TODO: make private after switching to yiisoft/di
    /**
     * @var Container
     */
    private $di;

    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * @param $command
     * @return CommandBus|null
     * @throws InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function getBusForCommand($command): ?CommandBus
    {
        $className = get_class($command);

        if (!isset($this->commandMiddlewares[$className])) {
            return null;
        }

        /**
         * @var Middleware[]
         */
        $middlewares = [];
        foreach ($this->commandMiddlewares[$className] as $middleware) {
            if (is_array($middleware)) {
                $middlewares[] = $this->di->get(array_shift($middleware), $middleware);
            } elseif (is_string($middleware)) {
                $middlewares[] = $this->di->get($middleware);
            } else {
                throw new InvalidConfigException('Unsupported middleware config');
            }
        }

        return new CommandBus($middlewares);
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $bus = $this->getBusForCommand($command);
        if ($bus === null) {
            return $next($command);
        }

        return $next($bus->handle($command));
    }

    /**
     * @return array
     */
    public function getCommandMiddlewares(): array
    {
        return $this->commandMiddlewares;
    }

    /**
     * @param array $commandMiddlewares
     */
    public function setCommandMiddlewares(array $commandMiddlewares): void
    {
        $this->commandMiddlewares = $commandMiddlewares;
    }
}
