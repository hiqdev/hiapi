<?php

namespace hiapi\event;

/**
 * Interface EventStorageInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface EventStorageInterface
{
    /**
     * @param object ...$events
     */
    public function store(...$events): void;

    /**
     * @return array
     */
    public function release(): array;
}
