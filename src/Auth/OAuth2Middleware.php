<?php

namespace hiapi\Core\Auth;

use hiapi\exceptions\NotAuthenticatedException;
use Psr\Http\Message\ServerRequestInterface;
use yii\web\User;
use GuzzleHttp\Client;
use yii\web\IdentityInterface;

/**
 * XXX Think if authentication can be done deferred?
 * XXX Does it make sence?
 */
class OAuth2Middleware extends AuthMiddleware
{
    public $userinfoUrl;

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function authenticate(ServerRequestInterface $request)
    {
        $identity = $this->findIdentity($request);

        if (empty($identity)) {
            throw new NotAuthenticatedException('failed login by token');
        }

        $ok = $this->user->login($identity);
        if ($ok !== true) {
            throw new NotAuthenticatedException('failed login by token');
        }
    }

    private function findIdentity(ServerRequestInterface $request): ?IdentityInterface
    {
        $token = $this->getAccessToken($request);
        if (empty($token)) {
            throw new NotAuthenticatedException('no access token given');
        }

        try {
            $info = $this->getUserInfo($token);
        } catch (\Throwable $e) {
            throw new NotAuthenticatedException($e->getMessage());
        }

        $class = $this->user->identityClass;

        return $class::fromArray($info);
    }

    private function getUserInfo(string $token)
    {
        $res = $this->getClient()->request('GET', '', ['headers' => [
            'Authorization' => 'Bearer ' . $token,
        ]]);

        return \json_decode((string)$res->getBody(), true);
    }

    private function getClient(): Client
    {
        return new Client([
            'base_uri' => $this->userinfoUrl,
            'timeout' => 1.0,
        ]);
    }

    private function getAccessToken(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
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
