<?php

namespace hiapi\commands;

use ArrayAccess;
use Countable;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;
use yii\base\Model;

/**
 * Class BulkCommand
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class BulkCommand extends BaseCommand implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var ArrayCollection
     */
    private $collection;
    /**
     * @var string
     */
    private $commandClassName;

    public static function of(string $className): self
    {
        $self = new self();
        $self->commandClassName = $className;

        return $self;
    }

    public function init(): void
    {
        parent::init();

        $this->collection = new ArrayCollection();
    }

    public function load($data, $formName = null): bool
    {
        $data = array_filter($data, 'is_array');

        for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
            $this->collection->add(new $this->commandClassName);
        }

        return self::loadMultiple($this->collection, $data, $formName);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return array_reduce($this->collection->toArray(), static function (bool $isValid, BaseCommand $command): bool {
            return $command->validate() && $isValid;
        }, true);
    }

    public function add($command): self
    {
        $this->collection->add($command);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->collection->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->collection->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        return $this->collection->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        return $this->collection->offsetUnset($offset);
    }
}
