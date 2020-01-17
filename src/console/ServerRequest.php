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

    public function getServerParams()
    {
        throw new \Exception('not implemented');
    }

    public function getCookieParams()
    {
        throw new \Exception('not implemented');
    }

    public function withCookieParams(array $cookies)
    {
        throw new \Exception('not implemented');
    }

    public function withQueryParams(array $query)
    {
        throw new \Exception('not implemented');
    }

    public function getUploadedFiles()
    {
        throw new \Exception('not implemented');
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        throw new \Exception('not implemented');
    }

    public function withParsedBody($data)
    {
        throw new \Exception('not implemented');
    }

    public function getAttributes()
    {
        throw new \Exception('not implemented');
    }

    public function getAttribute($name, $default = null)
    {
        throw new \Exception('not implemented');
    }

    public function withAttribute($name, $value)
    {
        throw new \Exception('not implemented');
    }

    public function withoutAttribute($name)
    {
        throw new \Exception('not implemented');
    }

    public function getRequestTarget()
    {
        throw new \Exception('not implemented');
    }

    public function withRequestTarget($requestTarget)
    {
        throw new \Exception('not implemented');
    }

    public function getMethod()
    {
        throw new \Exception('not implemented');
    }

    public function withMethod($method)
    {
        throw new \Exception('not implemented');
    }

    public function getUri()
    {
        throw new \Exception('not implemented');
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        throw new \Exception('not implemented');
    }

    public function getProtocolVersion()
    {
        throw new \Exception('not implemented');
    }

    public function withProtocolVersion($version)
    {
        throw new \Exception('not implemented');
    }

    public function getHeaders()
    {
        throw new \Exception('not implemented');
    }

    public function hasHeader($name)
    {
        throw new \Exception('not implemented');
    }

    public function getHeader($name)
    {
        throw new \Exception('not implemented');
    }

    public function getHeaderLine($name)
    {
        throw new \Exception('not implemented');
    }

    public function withHeader($name, $value)
    {
        throw new \Exception('not implemented');
    }

    public function withAddedHeader($name, $value)
    {
        throw new \Exception('not implemented');
    }

    public function withoutHeader($name)
    {
        throw new \Exception('not implemented');
    }

    public function getBody()
    {
        throw new \Exception('not implemented');
    }

    public function withBody(StreamInterface $body)
    {
        throw new \Exception('not implemented');
    }
}
