<?php
declare(strict_types=1);

namespace hiapi\Core\Endpoint\Middleware;

use Doctrine\Common\Collections\ArrayCollection;
use League\Tactician\Middleware;

/**
 * Class ReduceHandler takes a single command, packs it as
 * is was called like a bulk command, and passes it to the [[handler]].
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ReduceHandler implements Middleware
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
     */
    public function execute($command, callable $next)
    {
        $handler = $this->handler;

        $bulkCommand = new ArrayCollection([$command]);
        /** @var \IteratorAggregate $result */
        $result = $handler($bulkCommand);

        $resultArray = iterator_to_array($result, true);

        return reset($resultArray);
    }
}
