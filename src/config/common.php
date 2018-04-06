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
        /// BUS
            \hiapi\bus\ApiCommandsBusInterface::class => [
                '__class' => \hiapi\bus\ApiCommandsBus::class,
                '__construct()' => [
                    0 => \yii\di\Instance::of('bus.http-request'),
                ],
            ],
            'bus.per-command-middleware' => [
                '__class' => \hiapi\middlewares\PerCommandMiddleware::class,
            ],
        /// Bus accessories
            \League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor::class => \League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor::class,
            \League\Tactician\Handler\Locator\HandlerLocator::class => \hiqdev\yii2\autobus\bus\NearbyHandlerLocator::class,
            \League\Tactician\Handler\MethodNameInflector\MethodNameInflector::class => \League\Tactician\Handler\MethodNameInflector\HandleInflector::class,
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
            \Psr\Log\LoggerInterface::class => function () {
                return Yii::getLogger();
            }
        ],
    ],
];
