<?php

namespace hiapi\Core\Console;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

abstract class AbstractResponse extends Response
{
    public function __construct($data, int $status = 200, array $headers = [])
    {
        parent::__construct($this->prepareBody($data), $status, $this->prepareHeaders($headers));
    }

    protected function prepareHeaders(array $headers)
    {
        return $headers;
    }

    protected function prepareBody($data): Stream
    {
        return $this->createBodyFromString($this->formatData($data));
    }

    abstract protected function formatData($data): string;

    protected function createBodyFromString(string $string): Stream
    {
        $body = new Stream('php://temp', 'wb+');
        $body->write($string);
        $body->rewind();

        return $body;
    }
}
