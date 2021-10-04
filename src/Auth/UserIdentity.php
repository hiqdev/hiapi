<?php

namespace hiapi\Core\Auth;

use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Basic user identity.
 * We'll see if it is enough.
 */
class UserIdentity extends Model implements IdentityInterface
{
    public $id;
    public $username;
    public $state;
    public $roles = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id',          'integer'],
            ['username',    'string'],
            ['state',       'string'],
            ['roles',       'trim'],
        ];
    }

    /** {@inheritdoc} */
    public static function findIdentity($id)
    {
        throw new \yii\base\InvalidCallException('no `findIdentity` and not expected');
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\InvalidCallException('no `findIdentityByAccessToken` and not expected');
    }

    public static function fromArray(array $info): ?self
    {
        $id = $info['id'] ?? $info['sub'] ?? null;

        if (empty($id)) {
            return null;
        }

        return new static([
            'id'        => $id,
            'username'  => $info['username'] ?? $info['email'] ?? null,
            'state'     => $info['state'] ?? null,
            'roles'     => $info['roles'] ?? null,
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }
}
