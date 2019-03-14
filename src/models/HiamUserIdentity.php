<?php

namespace hiapi\models;

use hiqdev\yii\compat\yii;
use hiqdev\yii\DataMapper\query\Specification;
use hiqdev\yii\DataMapper\repositories\EntityNotFoundException;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\IdentityInterface;

/**
 * User identity model working with HIAM
 *
 */
class HiamUserIdentity extends Model implements IdentityInterface
{
    public $id;
    public $login;

    public $type;
    public $state;

    public $roles = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['login', 'string'],
            ['type', 'string'],
            ['state', 'string'],
            ['roles', 'trim'],
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
        $res = self::requestHiam('oauth/resource', ['access_token' => $token]);
        if (empty($res['id'])) {
            return null;
        }

        return new static([
            'id' => $res['id'],
            'login' => $res['login'],
            'roles' => $res['roles'],
            'type' => $res['type'],
            'state' => $res['state'],
        ]);
    }

    public static function requestHiam($path, $data)
    {
        $scheme = 'https';
        $host   = yii::getApp()->params['hiam.site'];
        $query  = http_build_query($data);
        $url    = "$scheme://$host/$path?$query";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);

        return Json::decode($res);
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
