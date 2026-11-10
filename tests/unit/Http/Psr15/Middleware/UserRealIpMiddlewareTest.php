<?php
declare(strict_types=1);

namespace hiapi\tests\unit\Http\Psr15\Middleware;

use hiapi\Core\Http\Psr15\Middleware\UserRealIpMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserRealIpMiddlewareTest extends TestCase
{
    private const TRUSTED_PROXY = '10.0.0.5';
    private const UNTRUSTED_CLIENT = '203.0.113.7';
    private const FORWARDED_CLIENT = '198.51.100.20';

    public static function clientIpProvider(): iterable
    {
        yield 'untrusted remote ignores the header' => [self::UNTRUSTED_CLIENT, self::FORWARDED_CLIENT, self::UNTRUSTED_CLIENT];
        yield 'trusted proxy without the header' => [self::TRUSTED_PROXY, null, self::TRUSTED_PROXY];
        yield 'trusted proxy forwards a valid IP' => [self::TRUSTED_PROXY, self::FORWARDED_CLIENT . ', 10.0.0.6', self::FORWARDED_CLIENT];
        yield 'trusted proxy forwards a non-IP value' => [self::TRUSTED_PROXY, 'unknown', self::TRUSTED_PROXY];
        yield 'trusted proxy forwards an empty value' => [self::TRUSTED_PROXY, '', self::TRUSTED_PROXY];
        yield 'trusted proxy forwards an IP with a port' => [self::TRUSTED_PROXY, self::FORWARDED_CLIENT . ':443', self::TRUSTED_PROXY];
    }

    #[DataProvider('clientIpProvider')]
    public function testClientIp(string $remoteAddr, ?string $forwardedFor, string $expectedIp): void
    {
        $headers = $forwardedFor === null ? [] : ['X-Forwarded-For' => $forwardedFor];
        $request = new ServerRequest(['REMOTE_ADDR' => $remoteAddr], [], '/', 'GET', 'php://memory', $headers);
        $handler = new class implements RequestHandlerInterface {
            public ?ServerRequestInterface $request = null;

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->request = $request;

                return new Response();
            }
        };

        (new UserRealIpMiddleware(['10.0.0.0/8' => 1]))->process($request, $handler);

        $this->assertSame($expectedIp, $handler->request->getAttribute(UserRealIpMiddleware::ATTRIBUTE_NAME));
    }
}
