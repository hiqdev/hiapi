<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Throwable;

final class Text extends ContentOnly
{
    public function formatContent($content, $attributes = []): string
    {
        if (!\is_array($content)) {
            return $this->formatValue($content);
        }

        $res = '';
        foreach ($content as $k => $v) {
            $res .= $k . ': ' . (\is_array($v) ? \implode(',', \array_map($this->formatValue(...), $v)) : $this->formatValue($v)) . "\n";
        }

        return $res;
    }

    /**
     * Never dumps an object: an exception's trace holds most of the application's object graph.
     */
    private function formatValue($value): string
    {
        if ($value instanceof Throwable) {
            return $value->getMessage();
        }

        if ($value === null || \is_scalar($value)) {
            return (string)$value;
        }

        return \get_debug_type($value);
    }
}
