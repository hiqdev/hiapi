<?php

namespace hiapi\Core\Http\Psr7\Response;

use Laminas\Diactoros\Response;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FatResponse
{
    public const REQUEST_ATTRIBUTE = 'request';

    public static function create($content, RequestInterface $request = null): ResponseInterface
    {
        if (is_array($content)) {
            $content = self::convertScintificNumbers($content);
        }
        if ($request === null) {
            return new UnformattedResponse(new Response(), $content);
        }

        return new UnformattedResponse(new Response(), $content, [
            self::REQUEST_ATTRIBUTE => $request,
        ]);
    }

    public static function getRequest(ResponseInterface $response): ?RequestInterface
    {
        if (!$response instanceof UnformattedResponse) {
            return null;
        }
        $request = $response->getAttributes()[self::REQUEST_ATTRIBUTE] ?? null;

        return $request instanceof RequestInterface ? $request : null;
    }

    public static function getContent(ResponseInterface $response)
    {
        return $response instanceof UnformattedResponse ? $response->getUnformattedContent() : null;
    }

    public static function convertScintificNumbers(array $content): array
    {
        if (empty($content)) {
            return $content;
        }
        foreach ($content as &$row) {
            foreach ($row as $key => $value) {
                if (is_numeric($value) && $pos = stripos($value, 'E') !== false) {
                    $decimals = intval(substr($value, $pos + 2, strlen($value))); // extract the decimals
                    $row[$key] = number_format($value, $decimals, '.', '');
                }
            }
        }
        return $content;
    }
}
