<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

use yii\di\Instance;

return array_filter([
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@vendor/bower' => '@vendor/bower-asset',
        '@vendor/npm' => '@vendor/npm-asset',
    ],
    'controllerNamespace' => 'hiapi\controllers',
    'bootstrap' => array_filter([
        'debug' => empty($params['debug.enabled']) ? null : 'debug',
    ]),
    'components' => [
        'request' => [
            'enableCsrfCookie' => false,
            'enableCsrfValidation' => false,
        ],
        'mailer' => [
            'viewPath' => '@hiapi/views/mail',
            'htmlLayout' => '@hiapi/views/layouts/mail-html',
            'textLayout' => '@hiapi/views/layouts/mail-text',
        ],
        'urlManager' => [
            '__class' => \yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'default' => [
                    'pattern' => '<version:v\d+>/<resource:[\w]+>/<action:[\w-]+>/<bulk:(bulk)?>',
                    'route' => 'api/command',
                    'defaults' => [
                        'version' => 'v1',
                        'bulk' => false,
                    ],
                ],
            ],
        ],
    ],
    'modules' => array_filter([
        'debug' => empty($params['debug.enabled']) ? null : array_filter([
            '__class' => \yii\debug\Module::class,
            'allowedIPs' => isset($params['debug.allowedIps']) ? $params['debug.allowedIps'] : null,
            'historySize' => isset($params['debug.historySize']) ? $params['debug.historySize'] : null,
        ]),
    ]),
    'container' => [
        'singletons' => [
            \hiapi\bus\ApiCommandsBusInterface::class => [
                '__class' => \hiapi\bus\ApiCommandsBus::class,
                '__construct()' => [
                    0 => Instance::of('bus.http-request'),
                ],
            ],
            \yii\base\Application::class => function () {
                return Yii::$app;
            },
            \yii\mail\MailerInterface::class => function () {
                return Yii::$app->get('mailer');
            },
        ],
    ],
]);
