<?php

namespace hiapi\event;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use yii\base\InvalidConfigException;
use yii\base\UnknownPropertyException;

/**
 * Class ConfigurableEmitter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ConfigurableEmitter extends Emitter implements EmitterInterface
{
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

            $this->addListener($listener['event'], $listener['listener'], $listener['priority'] ?? self::P_NORMAL);
        }

        return $this;
    }

    public function __set($name, $value)
    {
        if ($name === 'listeners') {
            $this->setListeners($value);
            return;
        }

        throw new UnknownPropertyException('Property "' . $name . '" is not available.');
    }
}
