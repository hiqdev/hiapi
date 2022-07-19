<?php
declare(strict_types=1);

namespace hiapi\Core\Endpoint;

interface ActionInterface
{
    public function __invoke($command);
}
