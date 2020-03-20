<?php

namespace hiapi\Core\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->authenticate($request);

        return $handler->handle($request);
    }

    abstract public function authenticate(ServerRequestInterface $request);

    protected function getAccessToken(ServerRequestInterface $request): ?string
    {
        return $this->getBearerToken($request) ?? $this->getParam($request, 'access_token');
    }

    protected function getBearerToken(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/^Bearer\s+([a-fA-F0-9]{30,50})$/', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getParam(ServerRequestInterface $request, string $name): ?string
    {
        return $request->getParsedBody()[$name] ?? $request->getQueryParams()[$name] ?? null;
    }
}
