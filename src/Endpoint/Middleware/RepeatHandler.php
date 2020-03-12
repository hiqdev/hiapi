<?php

namespace hiapi\Core\Endpoint\Middleware;

use Doctrine\Common\Collections\ArrayCollection;
use League\Tactician\Middleware;

class RepeatHandler implements Middleware
{
    /**
     * @var callable
     */
    private $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute($command, callable $next)
    {
        $this->ensureCommandIsIterable($command);

        $handler = $this->handler;
        $items = iterator_to_array($command, true);

        return new ArrayCollection(
            array_map(static function ($item) use ($handler) {
                return $handler($item);
            }, $items)
        );
    }

    private function ensureCommandIsIterable($command): void
    {
        if (!$command instanceof \IteratorAggregate) {
            throw new \RuntimeException(sprintf(
                '%s expects command to be iterable, %s provided instead',
                self::class,
                get_class($command)
            ));
        }
    }
}
