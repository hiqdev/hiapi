<?php

namespace hiapi\Core\Endpoint\Middleware;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use IteratorAggregate;
use League\Tactician\Middleware;

class TypeCheckMiddleware implements Middleware
{
    private Endpoint $endpoint;

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
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
                throw new \RuntimeException(sprintf(
                    'Endpoint "%s" expects IteratorAggregate of "%s" as input, got "%s" instead',
                    $this->endpoint->name,
                    $inputType->getEntriesClass(),
                    get_class($command),
                ));
            }

            foreach ($command as $item) {
                if (!is_a($item, $inputType->getEntriesClass())) {
                    throw new \RuntimeException(sprintf(
                        'Endpoint "%s" expects IteratorAggregate of "%s" as input, one of collection items is "%s"',
                        $this->endpoint->name,
                        $inputType->getEntriesClass(),
                        get_class($item),
                    ));
                }
            }

            return;
        }

        if (get_class($command) !== $inputType) {
            throw new \RuntimeException(sprintf(
                'Endpoint "%s" expects "%s" as input, got "%s" instead',
                $this->endpoint->name,
                $inputType,
                get_class($command),
            ));
        }
    }

    private function ensureResultIsCorrect($result): void
    {
        $returnType = $this->endpoint->returnType;
        if ($returnType instanceof Collection) {
            if (!$result instanceof DoctrineCollection) {
                throw new \RuntimeException(sprintf(
                    'Endpoint "%s" expects collection of "%s" as a result, got "%s" instead',
                    $this->endpoint->name,
                    $returnType->getEntriesClass(),
                    get_class($result),
                ));
            }

            foreach ($result as $item) {
                if (!is_a($item, $returnType->getEntriesClass())) {
                    throw new \RuntimeException(sprintf(
                        'Endpoint "%s" expects collection of "%s" as a result, one of collection items is "%s"',
                        $this->endpoint->name,
                        $returnType->getEntriesClass(),
                        get_class($item),
                    ));
                }
            }

            return;
        }

        if (is_subclass_of($result, $returnType)) {
            return;
        }

        if (get_class($result) !== $this->endpoint->returnType) {
            throw new \RuntimeException(sprintf(
                'Endpoint "%s" expects "%s" as a result, got "%s" instead',
                $this->endpoint->name,
                is_object($this->endpoint->returnType)
                    ? get_class($this->endpoint->returnType)
                    : $this->endpoint->returnType,
                get_class($result),
            ));
        }
    }
}
