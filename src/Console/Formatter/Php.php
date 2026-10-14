<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Throwable;

class Php extends ContentOnly
{
    public function formatContent($content, $attributes = []): string
    {
        // Formatters run outside the exception middleware, so re-throwing here escapes as an unhandled error.
        if ($content instanceof Throwable) {
            return var_export($content->getMessage(), true);
        }

        return var_export($content, true);
    }
}
