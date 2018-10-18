<?php

namespace hiapi\event;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

/**
 * Interface EventInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface EventInterface
{
    /**
     * Provides the event id
     *
     * @return UuidInterface
     */
    public function uuid(): UuidInterface;

    /**
     * @return DateTimeImmutable
     */
    public function createdAt(): DateTimeImmutable;

    /**
     * @return string The event name
     */
    public function type(): string;

    /**
     * Stop event propagation.
     */
    public function stopPropagation();

    /**
     * Check whether propagation was stopped.
     *
     * @return bool
     */
    public function isPropagationStopped();
}
