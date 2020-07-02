<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\Formatter\ContentOnly;

class Php extends ContentOnly
{
    public function formatContent($content, $attributes = []): string
    {
        return var_export($content, true);
    }
}
