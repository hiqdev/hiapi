<?php

namespace hiapi\Core\Console;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;

class ConsoleEmitter implements EmitterInterface
{
    public function emit(ResponseInterface $response) : bool
    {
        print $response->getBody();

        return true;
    }
}
