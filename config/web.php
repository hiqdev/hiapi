<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

use hiqdev\yii\compat\yii;
use hiqdev\yii\compat\Buildtime;

$aliases = [
    '@bower' => '@vendor/bower-asset',
    '@npm' => '@vendor/npm-asset',
    '@vendor/bower' => '@vendor/bower-asset',
    '@vendor/npm' => '@vendor/npm-asset',
];

$app = array_filter([
    'controllerNamespace' => 'hiapi\controllers',
    'bootstrap' => array_filter([
        'debug' => empty($params['debug.enabled']) ? null : 'debug',
    ]),
    'modules' => array_filter([
        'debug' => empty($params['debug.enabled']) ? null : array_filter([
            '__class' => \yii\debug\Module::class,
            'allowedIPs' => $params['debug.allowedIps'],
            'historySize' => $params['debug.historySize'],
        ]),
    ]),
]);

$components = [
    'request' => [
        'enableCsrfCookie' => false,
        'enableCsrfValidation' => false,
    ],
    'urlManager' => [
        '__class' => \yii\web\UrlManager::class,
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'enableStrictParsing' => true,
        'rules' => [
            'default' => [
                'pattern' => '<version:v\d+>/<resource:[\w-]+>/<action:[\w-]+>/<bulk:(bulk)?>',
                'route' => 'api/command',
                'defaults' => [
                    'version' => 'v1',
                    'bulk' => false,
                ],
            ],
        ],
    ],
];

$singletons = [
/// BUS
    'bus.responder-middleware' => [
        '__class' => ($_ENV['ENABLE_JSONAPI_RESPONSE'] ?? false)
            ? \hiapi\middlewares\JsonApiMiddleware::class
            : \hiapi\middlewares\LegacyResponderMiddleware::class,
    ],
    'bus.handle-exceptions-middleware' => \hiapi\middlewares\HandleExceptionsMiddleware::class,
    'bus.loader-middleware' => \hiqdev\yii2\autobus\bus\LoadFromRequestMiddleware::class,

];

return Buildtime::run(yii::is2()) ? array_merge([
    'aliases' => $aliases,
    'components' => $components,
    'container' => [
        'singletons' => $singletons,
    ],
], $app) : array_merge([
    'aliases' => $aliases,
    'app' => $app,
], $components, $singletons);
