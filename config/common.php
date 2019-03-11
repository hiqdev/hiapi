<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

use hiapi\yii;

$app = [
    'id' => 'hiapi',
    'name' => 'HiAPI',
    'basePath' => dirname(__DIR__) . '/src',
    'viewPath' => '@hiapi/views',
];

$components = [
    'user' => [
        '__class' => \yii\web\User::class,
    ],
];

$singletons = [
    \yii\web\User::class => [
        'identityClass' => \hiapi\models\HiamUserIdentity::class,
        'enableSession' => false,
    ],
/// BUS
    \hiapi\bus\ApiCommandsBusInterface::class => [
        '__class' => \hiapi\bus\ApiCommandsBus::class,
        '__construct()' => [
            yii::referenceTo('bus.the-bus'),
        ],
    ],
    'bus.per-command-middleware' => [
        '__class' => \hiapi\middlewares\PerCommandMiddleware::class,
    ],
    'bus.default-command-handler' => [
        '__class' => \League\Tactician\Handler\CommandHandlerMiddleware::class,
        '__construct()' => [
            yii::referenceTo(\League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor::class),
            yii::referenceTo(\hiqdev\yii2\autobus\bus\NearbyHandlerLocator::class),
            yii::referenceTo(\League\Tactician\Handler\MethodNameInflector\HandleInflector::class),
        ],
    ],
    'bus.the-bus' => [
        '__class' => \hiqdev\yii2\autobus\components\TacticianCommandBus::class,
        '__construct()' => [
            yii::referenceTo('bus.default-command-handler'),
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
    \yii\mail\MailerInterface::class => function () {
        return Yii::$app->get('mailer');
    },
];

$old_singletons = [
    \yii\base\Application::class => function () {
        return Yii::$app;
    },

    \Psr\Log\LoggerInterface::class => function () {
        return Yii::getLogger();
    },

    \Psr\Container\ContainerInterface::class => function ($container) {
        return new class($container) implements \Psr\Container\ContainerInterface {
            /**
             * @var \yii\di\Container
             */
            private $yiiContainer;

            public function __construct(\yii\di\Container $yiiContainer)
            {
                $this->yiiContainer = $yiiContainer;
            }

            public function get($id)
            {
                return $this->yiiContainer->get($id);
            }

            public function has($id)
            {
                return $this->yiiContainer->has($id);
            }
        };
    },
];

return class_exists('Yii') ? array_merge([
    'aliases' => $aliases,
    'logger' => [
        '__class' => \yii\log\Logger::class
    ],
    'components' => $components,
    'container' => [
        'singletons' => array_merge($singletons, $old_singletons),
    ],
    'params' => $params,
    'vendorPath' => '@root/vendor',
    'runtimePath' => '@root/runtime',
], $app) : array_merge([
    'aliases' => $aliases,
    'app' => $app,
], $components, $singletons);