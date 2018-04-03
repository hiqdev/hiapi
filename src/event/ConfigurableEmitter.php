<?php

namespace hiapi\event;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use League\Event\ListenerInterface;
use yii\base\InvalidConfigException;
use yii\base\UnknownPropertyException;
use yii\di\Container;
use yii\helpers\StringHelper;

/**
 * Class ConfigurableEmitter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ConfigurableEmitter extends Emitter implements EmitterInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * ConfigurableEmitter constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __set($name, $value)
    {
        if ($name === 'listeners') {
            $this->setListeners($value);
            return;
        }

        throw new UnknownPropertyException('Property "' . $name . '" is not available.');
    }

    /**
     * Allows to set listeners with standard Yii configuration system
     *
     * @param array[] $listeners
     * @return $this
     * @throws InvalidConfigException
     */
    public function setListeners(array $listeners = [])
    {
        foreach ($listeners as $listener) {
            if (!isset($listener['event']) || !isset($listener['listener'])) {
                throw new InvalidConfigException('Both "event" and "listener" properties are required to attach a listener.');
            }

            if (is_string($listener['listener'])) {
                $listener['listener'] = $this->listenerFromClassName($listener['listener']);
            }

            $this->addListener($listener['event'], $listener['listener'], $listener['priority'] ?? self::P_NORMAL);
        }

        return $this;
    }

    /**
     * @param $className
     * @return \Closure
     */
    private function listenerFromClassName(string $className): \Closure
    {
        return function ($event) use ($className) {
            /** @var ListenerInterface $handler */
            $listener = $this->container->get($className);

            $listener->handle($event);
        };
    }

    public function hasListeners($event)
    {
        if (parent::hasListeners($event)) {
            return true;
        }

        $namesWithWildcard = array_filter(array_keys($this->listeners), function ($name) {
            return $name !== '*' && strpos($name, '*') !== false;
        });
        foreach ($namesWithWildcard as $name) {
            if (StringHelper::matchWildcard('*', $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the listeners sorted by priority for a given event.
     *
     * @param string $event
     *
     * @return ListenerInterface[]
     */
    protected function getSortedListeners($event)
    {
        if (! $this->hasListeners($event)) {
            return [];
        }

        $listeners = $this->listeners[$event] ?? [];

        if ($event !== '*') {
            $namesWithWildcard = array_filter(array_keys($this->listeners), function ($name) {
                return $name !== '*' && strpos($name, '*') !== false;
            });
            foreach ($namesWithWildcard as $name) {
                if (StringHelper::matchWildcard('*', $name)) {
                    $listeners = array_merge($listeners, $this->listeners[$name]);
                }
            }
        }

        krsort($listeners);

        return call_user_func_array('array_merge', $listeners);
    }
}
