<?php

namespace hiapi\Core\Endpoint;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use hiapi\Core\Endpoint\Middleware\CheckPermissionsMiddleware;
use hiapi\Core\Endpoint\Middleware\TypeCheckMiddleware;
use hiapi\exceptions\ConfigurationException;
use hiapi\middlewares\CallableHandler;
use hiapi\middlewares\ValidateCommandMiddleware;
use hiqdev\yii\compat\yii;
use League\Tactician\CommandBus;
use League\Tactician\Middleware;
use Psr\Container\ContainerInterface;
use yii\base\Model;
use yii\web\User;

class EndpointProcessor
{
    /**
     * @var Endpoint
     */
    private $endpoint;
    /**
     * @var ContainerInterface
     */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param $command
     * @param Endpoint $endpoint
     * @return Model|ArrayCollection
     */
    public function __invoke($command, Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;

        $middlewares = [];
        $middlewares[] = new TypeCheckMiddleware($endpoint);
        $middlewares[] = new CheckPermissionsMiddleware($endpoint, $this->di->get(User::class));
        $middlewares[] = new ValidateCommandMiddleware();
        $middlewares = array_merge($middlewares, $this->endpointMiddlewares());

        $result = (new CommandBus($middlewares))->handle($command);

        return $result;
    }

    /**
     * Produces array of middlewares.
     *
     * TODO: Maybe, the Endpoint must prepare the middlewares array itself?
     *
     * @return Middleware[]
     */
    private function endpointMiddlewares(): array
    {
        return array_map(function ($item): Middleware {
            if (is_string($item)) {
                $item = $this->di->get($item);
            }

            if (is_array($item)) {
                $item = yii::createObject($item);
            }

            if ($item instanceof Middleware) {
                return $item;
            }

            if (is_object($item) && is_callable($item)) {
                $item = Closure::fromCallable($item);
            }

            if ($item instanceof \Closure) {
                return new CallableHandler($item);
            }

            throw new ConfigurationException(sprintf('Do not know how to instantiate %s', (string)$item));
        }, $this->endpoint->getMiddlewares());
    }
}
