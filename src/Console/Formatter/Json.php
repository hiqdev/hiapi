<?php

namespace hiapi\Core\Console\Formatter;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use hiapi\exceptions\SystemError;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Throwable;

// XXX maybe enable force object
// use const JSON_FORCE_OBJECT;

final class Json extends ContentOnly
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

    public function formatContent($content, array $attributes = []): string
    {
        if ($content instanceof Throwable) {
            return $this->formatException($content);
        }

        return $this->encode($content);
    }

    protected function formatException(Throwable $e): string
    {
        $system = $e instanceof SystemError;
        $source = $system ? $e->getPrevious() ?? $e : $e;

        return $this->encode([
            '_error' => $e->getMessage(),
            '_error_ops' => array_filter([
                'at' => Json::class,
                'class' => get_class($source),
                'data' =>  $system ? $e->getData() : null,
                'message' => getenv('ENV') === 'prod' ? '' : $source->getMessage(),
            ]),
        ]);
    }

    protected function encode($data): string
    {
        $res = json_encode($data, $this->flags | JSON_THROW_ON_ERROR);
        assert(is_string($res));

        return $res;
    }
}
