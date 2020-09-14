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

$app = [
    'id' => 'hiapi',
    'name' => 'HiAPI',
    'basePath' => dirname(__DIR__) . '/src',
    'viewPath' => '@hiapi/views',
];

$aliases = [
    '@hiapi' => dirname(__DIR__) . '/src',
];

$components = [
    (yii::is3() ? 'logger' : 'log') => [
        'targets' => [
            [
                '__class' => \yii\log\FileTarget::class,
                'logFile' => '@runtime/error.log',
                'levels' => [\Psr\Log\LogLevel::ERROR],
                'logVars' => [],
            ],
        ],
    ],
    'mailer' => [
        'viewPath' => '@hiapi/views/mail',
        'htmlLayout' => '@hiapi/views/layouts/mail-html',
        'textLayout' => '@hiapi/views/layouts/mail-text',
    ],
];

$singletons = array_merge(
    include __DIR__ . '/old-bus-request-handling.php',
    include __DIR__ . '/request-handling.php',
    [
        \yii\web\User::class => [
            'identityClass' => \hiapi\Core\Auth\UserIdentity::class,
            'enableSession' => false,
        ],

    /// Event
        \hiapi\event\EventStorageInterface::class => \hiapi\event\EventStorage::class,
        \League\Event\EmitterInterface::class => [
            '__class' => \hiapi\event\ConfigurableEmitter::class,
            'listeners' => array_filter([
                YII_ENV === 'dev'
                    ? ['event' => '*', 'listener' => \hiapi\event\listener\LogEventsListener::class]
                    : null,
            ]),
        ],

    /// Queue
        \PhpAmqpLib\Connection\AMQPStreamConnection::class => [
            '__class' => \PhpAmqpLib\Connection\AMQPLazyConnection::class,
            '__construct()' => [
                $params['amqp.host'],
                $params['amqp.port'],
                $params['amqp.user'],
                $params['amqp.password'],
            ],
        ],

    /// Request & response
        \Psr\Http\Message\ServerRequestInterface::class => function ($container) {
            return \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        },
        \Psr\Http\Message\ResponseInterface::class => function ($container) {
            return new \GuzzleHttp\Psr7\Response();
        },
        \WoohooLabs\Yin\JsonApi\Request\RequestInterface::class => \WoohooLabs\Yin\JsonApi\Request\Request::class,
        \WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface::class => \WoohooLabs\Yin\JsonApi\Request\JsonApiRequest::class, // Yin > 3.1.0
        \WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface::class => \WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory::class,

    /// General
        \yii\di\Container::class => function ($container) {
            return $container;
        },
        \yii\mail\MailerInterface::class => function () {
            return \hiqdev\yii\compat\yii::getApp()->get('mailer');
        },

        \hiapi\jsonApi\ResourceFactoryInterface::class => \hiapi\jsonApi\ResourceFactory::class,

        \hiapi\jsonApi\ResourceFactory::class => [
            '__construct()' => [
                'resourceMap' => [],
                new \Yiisoft\Arrays\Modifier\RemoveKeys(),
            ],
        ],
    ]
);

return yii::is3() ? array_merge([
    'app' => $app,
    'aliases' => $aliases,
], $components, $singletons) : array_merge([
    'aliases' => $aliases,
    'bootstrap' => ['log'],
    'components' => $components,
    'container' => [
        'resolveArrays' => true,
        'singletons' => $singletons,
    ],
    'params' => $params,
    'vendorPath' => '@root/vendor',
    'runtimePath' => '@root/runtime',
], $app);
