<?php

namespace hiapi\Core\Auth;

use hiqdev\yii\compat\yii;
use yii\helpers\Json;

/**
 * User identity model working with HIAM
 *
 */
class OAuth2UserIdentity extends UserIdentity
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
        return self::fromArray(self::getUserInfo($token));
    }

    public static function getUserInfo($token): array
    {
        $url = yii::getApp()->params['oauth2.userinfoUrl'] ?? null;
        if (empty($url)) {
            return [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return Json::decode((string)$res);
    }
}
