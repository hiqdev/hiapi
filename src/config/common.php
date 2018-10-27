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
            '__class' => \yii\web\User::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \yii\web\User::class => [
                'identityClass' => \hiapi\models\HiamUserIdentity::class,
                'enableSession' => false,
            ],
        /// BUS
            \hiapi\bus\ApiCommandsBusInterface::class => [
                '__class' => \hiapi\bus\ApiCommandsBus::class,
                '__construct()' => [
                    0 => new \yii\di\Reference('bus.the-bus'),
                ],
            ],
            'bus.per-command-middleware' => [
                '__class' => \hiapi\middlewares\PerCommandMiddleware::class,
            ],
            'bus.default-command-handler' => [
                '__class' => \League\Tactician\Handler\CommandHandlerMiddleware::class,
                '__construct()' => [
                    new \yii\di\Reference(\League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor::class),
                    new \yii\di\Reference(\hiqdev\yii2\autobus\bus\NearbyHandlerLocator::class),
                    new \yii\di\Reference(\League\Tactician\Handler\MethodNameInflector\HandleInflector::class),
                ],
            ],
            'bus.the-bus' => [
                '__class' => \hiqdev\yii2\autobus\components\TacticianCommandBus::class,
                '__construct()' => [
                    new \yii\di\Reference('bus.default-command-handler'),
                ],
                'middlewares' => [
                    'bus.responder-middleware',
                    'bus.handle-exceptions-middleware',
                    'bus.loader-middleware',
                    \hiqdev\yii2\autobus\bus\ValidateMiddleware::class,
                    \hiapi\middlewares\EventEmitterMiddleware::class,
                    'bus.per-command-middleware',
                ],
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
