<?php
declare(strict_types=1);

namespace hiapi\event;

/**
 * This class should be used to mark events with priority of execution.
 */
interface PriorityEventInterface
{
    /**
     * @return int|null a priority or `null` if event should not be prioritized
     */
    public function getPriority(): ?int;
}
