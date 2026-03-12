<?php

namespace hiapi\commands;

use ArrayAccess;
use Countable;
use Doctrine\Common\Collections\ArrayCollection;
use hiapi\Core\commands\CommandFactory;
use IteratorAggregate;

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

    public function __construct(private readonly string $commandClassName, private readonly CommandFactory $factory, $config = [])
    {
        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();

        $this->collection = new ArrayCollection();
    }

    public static function of(string $className, CommandFactory $factory): self
    {
        return new self($className, $factory);
    }

    #[\Override]
    public function load($data, $formName = null): bool
    {
        $data = array_filter($data, is_array(...));

        for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
            $this->collection->add($this->factory->createByClass($this->commandClassName));
        }

        return self::loadMultiple($this->collection, $data, $formName);
    }

    #[\Override]
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return array_reduce($this->collection->toArray(), static fn(bool $isValid, BaseCommand $command): bool => $command->validate() && $isValid, true);
    }

    public function add($command): self
    {
        $this->collection->add($command);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->collection->count();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function offsetExists($offset)
    {
        return $this->collection->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function offsetGet($offset)
    {
        return $this->collection->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function offsetSet($offset, $value)
    {
        return $this->collection->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function offsetUnset($offset)
    {
        return $this->collection->offsetUnset($offset);
    }
}
