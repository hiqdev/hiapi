<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\exceptions\NotAuthenticatedException;
use hiapi\legacy\lib\deps\dbc;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlacklistMiddleware implements MiddlewareInterface
{
    private $dbc;

    public function __construct(dbc $dbc)
    {
        $this->dbc = $dbc;
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
        $qip = $this->dbc->quote($ip);
        $found = $this->dbc->value("
            SELECT      1
            FROM        blacklisted
            WHERE       client_id = root_client_id()
                    AND type_id = ztype_id('blacklisted,ip')
                    AND state_id = state_id('blacklisted,ok')
                    AND str2inet(name) >>= str2inet($qip)
        ");
        return (bool)$found;
    }

    private function getIp(ServerRequestInterface $request): string
    {
        $ip = $request->getAttribute(ClientIpMiddleware::ATTRIBUTE_NAME);
        if (!empty($ip)) {
            return $ip;
        }

        $params = $request->getServerParams();

        return $params['REMOTE_ADDR'] ?? '';
    }
}
