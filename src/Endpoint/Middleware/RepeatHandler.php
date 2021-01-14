<?php

namespace hiapi\Core\Endpoint\Middleware;

use Doctrine\Common\Collections\ArrayCollection;
use League\Tactician\Middleware;

class RepeatHandler implements Middleware
{
    /** @var Middleware|callable */
    private $handler;

    public function __construct($handler)
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
        $items = iterator_to_array($command, true);
        $res = array_map(function ($item) {
            return $this->handle($item);
        }, $items);

        return new ArrayCollection($res);
    }

    private function handle($item)
    {
        $handler = $this->handler;
        if (is_callable($handler)) {
            return $handler($item);
        } elseif ($handler instanceof Middleware) {
            return $handler->execute($item, fn ($command) => $command);
        }
        throw new \RuntimeException(sprintf(
            '%s expects handler to be closure or Middleware',
            self::class
        ));
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
