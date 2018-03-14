<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

return [
    'id' => 'hiapi',
    'name' => 'HiAPI',
    'basePath' => dirname(__DIR__),

    /// aliases must be set before their use
    'aliases' => [],
    'viewPath' => '@hiapi/views',
    'vendorPath' => '@root/vendor',
    'runtimePath' => '@root/runtime',

    'logger' => [
        '__class' => \yii\log\Logger::class
    ],
    'components' => [
        'user' => [
            'identityClass' => \hiapi\models\HiamUserIdentity::class,
            'enableSession' => false,
        ],
    ],
    'container' => [
        'singletons' => [
            \yii\web\User::class => function ($container, $params, $config) {
                return new \yii\web\User($config);
            },
        ],
    ],
];
