<?php

namespace hiapi\Core\Console\Formatter;

use Lcobucci\ContentNegotiation\Formatter;
use Throwable;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final class Json implements Formatter
{
    private const DEFAULT_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;

    /**
     * @var int
     */
    private $flags;

    public function __construct(int $flags = self::DEFAULT_FLAGS)
    {
        $this->flags = $flags;
    }

    /**
     * {@inheritdoc}
     */
    public function format($content, array $attributes = []): string
    {
        if ($content instanceof Throwable) {
            return $this->formatException($content);
        }

        return $this->encode($content);
    }

    protected function formatException(Throwable $e): string
    {
        $message = $e->getMessage();

        return $this->encode([
            '_error' => $message,
            '_error_ops' => [
                'class' => get_class($e),
                # XXX causes Uncaught JsonException: Recursion detected
                # TODO fix and return the trace back
                #'trace' => $e->getTrace()
            ],
        ]);
    }

    protected function encode($data): string
    {
        $res = json_encode($data, $this->flags | JSON_THROW_ON_ERROR);
        assert(is_string($res));

        return $res;
    }
}
