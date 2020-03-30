<?php

namespace hiapi\Core\Auth;

use GuzzleHttp\RequestOptions;
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
        $res = $this->getClient()->request('GET', '', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return \json_decode((string)$res->getBody(), true);
    }

    private function getClient(): Client
    {
        return new Client([
            'base_uri' => $this->userinfoUrl,
            'timeout' => 5.0,
        ]);
    }
}
