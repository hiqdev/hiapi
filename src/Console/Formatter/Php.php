<?php

namespace hiapi\Core\Console\Formatter;

use Exception;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;

class Php extends ContentOnly
{
    public function formatContent($content, $attributes = []): string
    {
        if ($content instanceof Exception) {
            throw $content;
        }

        return var_export($content, true);
    }
}
