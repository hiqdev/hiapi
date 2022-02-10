<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    protected array $headers = [
        'Access-Control-Allow-Origin' => ['*'],
        'Access-Control-Allow-Methods' => ['GET, POST'],
        'Access-Control-Allow-Headers' => ['content-type, authorization'],
        'Access-Control-Max-Age' => ['600'],
    ];
    public bool $interceptOptionsRequests = false;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(array $headers = [], ResponseFactoryInterface $responseFactory)
    {
        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }
        $this->responseFactory = $responseFactory;
    }

    public function addHeader($name, $value): self
    {
        $this->headers[$name] = array_unique(array_merge($this->headers[$name] ?? [], [$value]));

        return $this;
    }

    public function withHeader($name, $value): self
    {
        return (clone $this)->addHeader($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->interceptOptionsRequests && $request->getMethod() === 'OPTIONS') {
            return $this->addHeaders($this->responseFactory->createResponse(201));
        }

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
