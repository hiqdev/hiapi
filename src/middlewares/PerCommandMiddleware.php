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
    private $commandMiddlewares;
    /**
     * @var Container
     */
    private $di;

    public function __construct(array $commandMiddlewares, Container $di)
    {
        $this->commandMiddlewares = $commandMiddlewares;
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
}
