<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    protected $headers = [
        'Access-Control-Allow-Origin' => ['*'],
        'Access-Control-Request-Method' => ['GET POST'],
    ];

    public function addHeader($name, $value): self
    {
        $this->headers[$name] = array_merge($this->headers[$name] ?? [], [$value]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $this->addHeaders($response);
    }

    private function addHeaders(ResponseInterface $response)
    {
        foreach ($this->headers as $name => $headers) {
            $response = $response->withHeader($name, $headers);
        }

        return $response;
    }
}
