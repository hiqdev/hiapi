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
    (Buildtime::run(yii::is3()) ? 'logger' : 'log') => [
        'targets' => [
            'file' => [
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
    Buildtime::run(include __DIR__ . '/old-bus-request-handling.php'),
    Buildtime::run(include __DIR__ . '/request-handling.php'),
    [
        \yii\web\User::class => [
            'identityClass' => \hiapi\Core\Auth\UserIdentity::class,
            'enableSession' => false,
        ],
        \hiapi\Service\Customer\AccountClientIdResolverInterface::class => \hiapi\Service\Customer\AccountClientIdResolver::class,

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
        \Psr\EventDispatcher\EventDispatcherInterface::class => \Yiisoft\EventDispatcher\Dispatcher\Dispatcher::class,
        \Psr\EventDispatcher\ListenerProviderInterface::class => \Yiisoft\EventDispatcher\Provider\Provider::class,

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
        \Psr\Http\Message\ServerRequestInterface::class => static function () {
            return \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        },
        \Psr\Http\Message\ResponseInterface::class => static function () {
            return new \GuzzleHttp\Psr7\Response();
        },
        \WoohooLabs\Yin\JsonApi\Request\RequestInterface::class => \WoohooLabs\Yin\JsonApi\Request\Request::class,
        \WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface::class => \WoohooLabs\Yin\JsonApi\Request\JsonApiRequest::class,// Yin > 3.1.0
        \WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface::class => \WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory::class,

        /// General
        \yii\di\Container::class => function ($container) {
            return $container;
        },
        \yii\mail\MailerInterface::class => function () {
            return \hiqdev\yii\compat\yii::getApp()->get('mailer');
        },

        \hiapi\jsonApi\ResourceDocumentFactoryInterface::class => \hiapi\jsonApi\ResourceDocumentFactory::class,

        \hiapi\jsonApi\ResourceDocumentFactory::class => [
            '__construct()' => [
                'resourceMap' => [],
                Buildtime::run(new \Yiisoft\Composer\Config\Merger\Modifier\RemoveKeys()),
            ],
        ],
    ]
);

return class_exists(\Yiisoft\Factory\Definition\Reference::class) ? array_merge([
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
