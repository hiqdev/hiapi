<?php

namespace hiapi\Core\Endpoint\Middleware;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use IteratorAggregate;
use League\Tactician\Middleware;
use RuntimeException;

class TypeCheckMiddleware implements Middleware
{
    public function __construct(private readonly Endpoint $endpoint)
    {
    }

    /**
     * @inheritDoc
     */
    public function execute($command, callable $next)
    {
        $this->ensureInputIsCorrect($command);
        $result = $next($command);
        $this->ensureResultIsCorrect($result);

        return $result;
    }

    private function ensureInputIsCorrect($command): void
    {
        $inputType = $this->endpoint->inputType;
        if ($inputType instanceof Collection) {
            if (!$command instanceof IteratorAggregate) {
                throw new RuntimeException(sprintf(
                    'Endpoint "%s" expects IteratorAggregate of "%s" as input, got "%s" instead',
                    $this->endpoint->name,
                    $inputType->getEntriesClass(),
                    $command::class,
                ));
            }

            foreach ($command as $item) {
                if (!is_a($item, $inputType->getEntriesClass())) {
                    throw new RuntimeException(sprintf(
                        'Endpoint "%s" expects IteratorAggregate of "%s" as input, one of collection items is "%s"',
                        $this->endpoint->name,
                        $inputType->getEntriesClass(),
                        $item::class,
                    ));
                }
            }

            return;
        }

        if ($command::class !== $inputType) {
            throw new RuntimeException(sprintf(
                'Endpoint "%s" expects "%s" as input, got "%s" instead',
                $this->endpoint->name,
                $inputType,
                $command::class,
            ));
        }
    }

    private function ensureResultIsCorrect($result): void
    {
        $returnType = $this->endpoint->returnType;

        if ($result === null) {
            if ($this->endpoint->returnIsNullable) {
                return;
            }

            throw new RuntimeException(sprintf(
                'Endpoint "%s" expects "%s" as a result, got "NULL" instead',
                $this->endpoint->name,
                is_object($this->endpoint->returnType)
                    ? $this->endpoint->returnType::class
                    : $this->endpoint->returnType,
            ));
        }

        if ($returnType instanceof Collection) {
            if (!$result instanceof DoctrineCollection) {
                throw new RuntimeException(sprintf(
                    'Endpoint "%s" expects collection of "%s" as a result, got "%s" instead',
                    $this->endpoint->name,
                    $returnType->getEntriesClass(),
                    $result::class,
                ));
            }

            foreach ($result as $item) {
                if (!is_a($item, $returnType->getEntriesClass())) {
                    throw new RuntimeException(sprintf(
                        'Endpoint "%s" expects collection of "%s" as a result, one of collection items is "%s"',
                        $this->endpoint->name,
                        $returnType->getEntriesClass(),
                        $item::class,
                    ));
                }
            }

            return;
        }

        if (is_subclass_of($result, $returnType)) {
            return;
        }

        if ($result::class !== $this->endpoint->returnType) {
            throw new RuntimeException(sprintf(
                'Endpoint "%s" expects "%s" as a result, got "%s" instead',
                $this->endpoint->name,
                is_object($this->endpoint->returnType)
                    ? $this->endpoint->returnType::class
                    : $this->endpoint->returnType,
                $result::class,
            ));
        }
    }
}
