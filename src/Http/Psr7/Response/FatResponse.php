<?php

namespace hiapi\Core\Http\Psr7\Response;

use Laminas\Diactoros\Response;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FatResponse
{
    public static function create($data, RequestInterface $request = null): ResponseInterface
    {
        if ($request === null) {
            return new UnformattedResponse(new Response(), $data);
        }

        return new UnformattedResponse(new Response(), $data, [
            'request' => $request,
        ]);
    }

    public static function getRequest(ResponseInterface $response): ?RequestInterface
    {
        if (!$response instanceof UnformattedResponse) {
            return null;
        }
        $request = $response->getAttributes()['request'] ?? null;

        return $request instanceof RequestInterface ? $request : null;
    }
}
