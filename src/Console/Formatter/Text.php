<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\ContentFormatter;

final class Text extends ContentFormatter
{
    public function formatContent($content, $attributes = []): string
    {
        if (\is_string($content)) {
            return $content;
        }

        if (!\is_array($content)) {
            return \var_export($content, true);
        }

        $res = '';
        foreach ($content as $k => $v) {
            $res .= $k.': ' . (\is_array($v) ? \implode(',', $v) : $v) . "\n";
        }

        return $res;
    }
}
