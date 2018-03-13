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
        /// BUS
            \hiapi\bus\ApiCommandsBusInterface::class => [
                '__class' => \hiapi\bus\ApiCommandsBus::class,
                '__construct()' => [
                    0 => Instance::of('bus.http-request'),
                ],
            ],
            'bus.http-request' => [
                '__class' => \hiqdev\yii2\autobus\components\TacticianCommandBus::class,
                '__construct()' => [
                    Instance::of('bus.http-request.default-command-handler'),
                ],
                'middlewares' => [
                    $_ENV['ENABLE_JSONAPI_RESPONSE'] ?? false
                        ? \hiapi\middlewares\JsonApiMiddleware::class
                        : \hiapi\middlewares\LegacyResponderMiddleware::class,
                    \hiapi\middlewares\HandleExceptionsMiddleware::class,
                    \hiqdev\yii2\autobus\bus\LoadFromRequestMiddleware::class,
                    \hiqdev\yii2\autobus\bus\ValidateMiddleware::class,
                    'bus.per-command-middleware',
                ],
            ],
            'bus.http-request.default-command-handler' => [
                '__class' => \League\Tactician\Handler\CommandHandlerMiddleware::class,
                '__construct()' => [
                    Instance::of(\League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor::class),
                    Instance::of(\hiqdev\yii2\autobus\bus\NearbyHandlerLocator::class),
                    Instance::of(\League\Tactician\Handler\MethodNameInflector\HandleInflector::class),
                ],
            ],
            'bus.per-command-middleware' => [
                '__class' => \hiapi\middlewares\PerCommandMiddleware::class,
            ],

        /// Bus accessories
            \League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor::class => \League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor::class,
            \League\Tactician\Handler\Locator\HandlerLocator::class => \hiqdev\yii2\autobus\bus\NearbyHandlerLocator::class,
            \League\Tactician\Handler\MethodNameInflector\MethodNameInflector::class => \League\Tactician\Handler\MethodNameInflector\HandleInflector::class,

            \hiqdev\yii2\autobus\components\CommandFactoryInterface::class => \hiqdev\yii2\autobus\components\SimpleCommandFactory::class,

        /// Request & response
            \Psr\Http\Message\ServerRequestInterface::class => function ($container) {
                return \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
            },
            \Psr\Http\Message\ResponseInterface::class => function ($container) {
                return new \GuzzleHttp\Psr7\Response();
            },
            \WoohooLabs\Yin\JsonApi\Request\RequestInterface::class => \WoohooLabs\Yin\JsonApi\Request\Request::class,
            \WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface::class => \WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory::class,
        /// General
            \yii\di\Container::class => function ($container) {
                return $container;
            },
            \yii\base\Application::class => function () {
                return Yii::$app;
            },
            \yii\mail\MailerInterface::class => function () {
                return Yii::$app->get('mailer');
            },
        ],
    ],
]);
