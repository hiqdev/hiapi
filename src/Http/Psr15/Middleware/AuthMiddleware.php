<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\exceptions\NotAuthenticatedException;
use hiapi\legacy\lib\mrdpAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var mrdpAuth
     */
    private $auth;


    public function __construct(mrdpAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->passIp($request);
        if ($this->login($request)) {
            return $handler->handle($request);
        }

        throw new NotAuthenticatedException($this->auth->get('error'));
    }

    private function login(ServerRequestInterface $request): bool
    {
        $token = $this->getAccessToken($request);
        if ($token) {
            return $this->auth->loginOauth2($token, '');
        }

        $login = $this->getParam($request, 'auth_login');
        if ($login) {
            return $this->auth->loginPasswd($login, $this->getParam($request, 'auth_password'));
        }

        return $this->auth->checkLite();
    }

    private function passIp($request): void
    {
        $ip = $request->getAttribute(ClientIpMiddleware::ATTRIBUTE_NAME);
        if (!empty($ip)) {
            $this->auth->set('ip', $ip);
        }
    }

    private function getAccessToken(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeader('Authorization');
        if (preg_match('/^Bearer\s+([a-fA-F0-9]{30,50})$/', $header, $matches)) {
            return $matches[1];
        }

        return $this->getParam($request, 'access_token');
    }

    public function getParam(ServerRequestInterface $request, string $name): ?string
    {
        return $request->getParsedBody()[$name] ?? $request->getQueryParams()[$name] ?? null;
    }
}
