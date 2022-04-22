<?php
declare(strict_types=1);

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\Core\Utils\CIDR;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserRealIpMiddleware implements MiddlewareInterface
{
    public const ATTRIBUTE_NAME = 'user-real-ip';
    /**
     * @var string[] Networks than are allowed to override client IP
     */
    private array $trustedNets;

    public function __construct(array $trustedNets)
    {
        $this->trustedNets = $trustedNets;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($this->prepare($request));
    }

    private function prepare(ServerRequestInterface $request): ServerRequestInterface
    {
        $clientIp = $this->getClientIp($request);
        $request = $request->withAttribute(self::ATTRIBUTE_NAME, $clientIp);

        if (!$this->isIpTrusted($clientIp)) {
            return $request;
        }

        $userIp = $this->getUserIp($request);
        if ($userIp === '' || $userIp === $clientIp) {
            return $request;
        }

        return $this->setNewIp($request, $userIp);
    }

    private function isIpTrusted(string $ip): bool
    {
        return CIDR::matchBulk($ip, $this->trustedNets);
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '';

        if (!$this->isIpTrusted($ip) || !$request->hasHeader('X-Forwarded-For')) {
            return $ip;
        }

        $ipsChain = array_map('trim', explode(',', $request->getHeaderLine('X-Forwarded-For')));

        return filter_var($ipsChain[0] ?? '', FILTER_VALIDATE_IP) ?? $ip;
    }

    private function getUserIp(ServerRequestInterface $request): string
    {
        $change = $request->getHeaderLine('X-User-Ip') ?: $this->getParam($request, 'auth_ip');

        return filter_var($change, FILTER_VALIDATE_IP) ?: '';
    }

    private function setNewIp(ServerRequestInterface $request, string $ip)
    {
        unset($_REQUEST['auth_ip']);

        return $request->withAttribute(self::ATTRIBUTE_NAME, $ip);
    }

    private function getParam(ServerRequestInterface $request, string $name): ?string
    {
        return $request->getParsedBody()[$name] ?? $request->getQueryParams()[$name] ?? null;
    }
}
