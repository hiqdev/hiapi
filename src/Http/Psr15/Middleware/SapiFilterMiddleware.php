<?php
declare(strict_types=1);

namespace hiapi\Core\Http\Psr15\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;

class SapiFilterMiddleware implements MiddlewareInterface
{
    private array $allowedSapiTypes = [];

    public static function cli(): callable
    {
        return static function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $self = new self;
            $self->allowedSapiTypes = ['cli'];

            return $self->process($request, $handler);
        };

    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array(PHP_SAPI, $this->allowedSapiTypes, true)) {
            return $handler->handle($request);
        }

        return new Response(Status::MISDIRECTED_REQUEST);
    }
}
