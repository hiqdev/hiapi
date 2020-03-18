<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\exceptions\NotAuthenticatedException;
use hiapi\Core\Utils\CIDR;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlacklistMiddleware implements MiddlewareInterface
{
    private $restriction;

    public function __construct($restriction)
    {
        $this->restriction = $restriction;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $this->getIp($request);
        if ($this->isForbidden($ip)) {
            throw new NotAuthenticatedException('Forbidden IP: ' . $ip);
        }

        return $handler->handle($request);
    }

    private function isForbidden(string $ip): bool
    {
        if (is_array($this->restriction)) {
            return CIDR::matchBulk($ip, $this->restriction);
        } elseif ($this->restriction instanceof \Closure) {
            return call_user_func($this->restriction, $ip);
        }
        return false;
    }

    private function getIp(ServerRequestInterface $request): string
    {
        $ip = $request->getAttribute(UserRealIpMiddleware::ATTRIBUTE_NAME);
        if (!empty($ip)) {
            return $ip;
        }

        $params = $request->getServerParams();

        return $params['REMOTE_ADDR'] ?? '';
    }
}
