<?php


namespace hiapi\Core\Http\Psr15\Middleware;


use hiapi\Core\Http\Psr7\Response\FatResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class EmptyEndpointMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/emptyEndpoint') {
            return FatResponse::create([], $request);
        }

        return $handler->handle($request);
    }
}
