<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\Formatter;

class Php implements Formatter
{
    public function format($content, array $attributes = []): string
    {
        return var_export($content, true);
    }
}
