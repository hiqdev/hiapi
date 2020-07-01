<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\ContentFormatter;

class Php extends ContentFormatter
{
    public function formatContent($content, $attributes = []): string
    {
        return var_export($content, true);
    }
}
