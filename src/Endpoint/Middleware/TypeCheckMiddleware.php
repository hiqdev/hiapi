<?php

namespace hiapi\Core\Endpoint\Middleware;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use IteratorAggregate;
use League\Tactician\Middleware;

class TypeCheckMiddleware implements Middleware
{
    /**
     * @var Endpoint
     */
    private $endpoint;

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
        $inputType = $this->endpoint->getInputType();
        if ($inputType instanceof Collection) {
            if (!$command instanceof IteratorAggregate) {
                throw new \RuntimeException(sprintf(
                    'Endpoint "%s" expects IteratorAggregate of "%s" as input, got "%s" instead',
                    $this->endpoint->getName(),
                    $inputType->getEntriesClass(),
                    get_class($command),
                ));
            }

            foreach ($command as $item) {
                if (get_class($item) !== $inputType->getEntriesClass()) {
                    throw new \RuntimeException(sprintf(
                        'Endpoint "%s" expects IteratorAggregate of "%s" as input, one of collection items is "%s"',
                        $this->endpoint->getName(),
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
                $this->endpoint->getName(),
                $inputType,
                get_class($command),
            ));
        }
    }

    private function ensureResultIsCorrect($result): void
    {
        $returnType = $this->endpoint->getReturnType();
        if ($returnType instanceof Collection) {
            if (!$result instanceof DoctrineCollection) {
                throw new \RuntimeException(sprintf(
                    'Endpoint "%s" expects collection of "%s" as a result, got "%s" instead',
                    $this->endpoint->getName(),
                    $returnType->getEntriesClass(),
                    get_class($result),
                ));
            }

            foreach ($result as $item) {
                if (get_class($item) !== $returnType->getEntriesClass()) {
                    throw new \RuntimeException(sprintf(
                        'Endpoint "%s" expects collection of "%s" as a result, one of collection items is "%s"',
                        $this->endpoint->getName(),
                        $returnType->getEntriesClass(),
                        get_class($item),
                    ));
                }
            }

            return;
        }


        if (get_class($result) !== $this->endpoint->getReturnType()) {
            throw new \RuntimeException(sprintf(
                'Endpoint "%s" expects "%s" as a result, got "%s" instead',
                $this->endpoint->getName(),
                $this->endpoint->getReturnType(),
                get_class($result),
            ));
        }
    }
}
