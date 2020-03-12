<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\legacy\lib\deps\err;
use Laminas\Diactoros\Response;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class QuietMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $this->isQuiet($request, $response) ? new Response() : $response;
    }

    private function isQuiet(ServerRequestInterface $request, ResponseInterface $response): bool
    {
        if (empty($request->getAttribute('quiet'))) {
            return false;
        }

        if (!$response instanceof UnformattedResponse) {
            return false;
        }

        $data = $response->getUnformattedContent();
        if ($data instanceof \Throwable) {
            return false;
        }
        if (is_array($data) && err::is($data)) {
            return false;
        }

        return true;
    }
}
