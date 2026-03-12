<?php

namespace hiapi\event;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class AbstractEvent
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class AbstractEvent extends \League\Event\AbstractEvent implements EventInterface
{
    /**
     * @var UuidInterface
     */
    private $uuid;
    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * AbstractEvent constructor.
     *
     * @param null $target
     * @throws \Exception
     */
    public function __construct(protected $target = null)
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @return object|null
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @inheritdoc
     */
    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @inheritdoc
     */
    public function type(): string
    {
        return $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getName()
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'uuid' => $this->uuid(),
            'name' => $this->getName(),
            'createdAt' => $this->createdAt(),
        ];
    }
}
