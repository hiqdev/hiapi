<?php

namespace hiapi\console;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PSR-7 compatible console ServerRequest
 *
 * XXX
 * XXX NOT finished, NOT used
 * XXX
 * @
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ServerRequest implements ServerRequestInterface
{

    protected $route;

    protected $params;

    public function getQueryParams()
    {
        if ($this->params === null) {
            $this->params = $this->parseParams();
        }

        return $this->params;
    }

    protected function parseParams()
    {
        if (isset($_SERVER['argv'])) {
            $args = $_SERVER['argv'];
            array_shift($this->_params);
        } else {
            $args = [];
        }
        foreach ($args as $arg) {
            var_dump($arg);
        }
        die;
    }

    public function getParsedBody()
    {
        return [];
    }

    public function getServerParams(): never
    {
        throw new \Exception('not implemented');
    }

    public function getCookieParams(): never
    {
        throw new \Exception('not implemented');
    }

    public function withCookieParams(array $cookies): never
    {
        throw new \Exception('not implemented');
    }

    public function withQueryParams(array $query): never
    {
        throw new \Exception('not implemented');
    }

    public function getUploadedFiles(): never
    {
        throw new \Exception('not implemented');
    }

    public function withUploadedFiles(array $uploadedFiles): never
    {
        throw new \Exception('not implemented');
    }

    public function withParsedBody($data): never
    {
        throw new \Exception('not implemented');
    }

    public function getAttributes(): never
    {
        throw new \Exception('not implemented');
    }

    public function getAttribute($name, $default = null): never
    {
        throw new \Exception('not implemented');
    }

    public function withAttribute($name, $value): never
    {
        throw new \Exception('not implemented');
    }

    public function withoutAttribute($name): never
    {
        throw new \Exception('not implemented');
    }

    public function getRequestTarget(): never
    {
        throw new \Exception('not implemented');
    }

    public function withRequestTarget($requestTarget): never
    {
        throw new \Exception('not implemented');
    }

    public function getMethod(): never
    {
        throw new \Exception('not implemented');
    }

    public function withMethod($method): never
    {
        throw new \Exception('not implemented');
    }

    public function getUri(): never
    {
        throw new \Exception('not implemented');
    }

    public function withUri(UriInterface $uri, $preserveHost = false): never
    {
        throw new \Exception('not implemented');
    }

    public function getProtocolVersion(): never
    {
        throw new \Exception('not implemented');
    }

    public function withProtocolVersion($version): never
    {
        throw new \Exception('not implemented');
    }

    public function getHeaders(): never
    {
        throw new \Exception('not implemented');
    }

    public function hasHeader($name): never
    {
        throw new \Exception('not implemented');
    }

    public function getHeader($name): never
    {
        throw new \Exception('not implemented');
    }

    public function getHeaderLine($name): never
    {
        throw new \Exception('not implemented');
    }

    public function withHeader($name, $value): never
    {
        throw new \Exception('not implemented');
    }

    public function withAddedHeader($name, $value): never
    {
        throw new \Exception('not implemented');
    }

    public function withoutHeader($name): never
    {
        throw new \Exception('not implemented');
    }

    public function getBody(): never
    {
        throw new \Exception('not implemented');
    }

    public function withBody(StreamInterface $body): never
    {
        throw new \Exception('not implemented');
    }
}
