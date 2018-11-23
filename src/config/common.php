<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

use yii\di\Reference;

return [
    'app' => [
        'id' => 'hiapi',
        'name' => 'HiAPI',
        'basePath' => dirname(__DIR__),
        'viewPath' => '@hiapi/views',
    ],

    \yii\web\User::class => [
        'identityClass' => \hiapi\models\HiamUserIdentity::class,
        'enableSession' => false,
    ],
/// BUS
    \hiapi\bus\ApiCommandsBusInterface::class => [
        '__class' => \hiapi\bus\ApiCommandsBus::class,
        '__construct()' => [
            0 => Reference::to('bus.the-bus'),
        ],
    ],
    'bus.per-command-middleware' => [
        '__class' => \hiapi\middlewares\PerCommandMiddleware::class,
    ],
    'bus.default-command-handler' => [
        '__class' => \League\Tactician\Handler\CommandHandlerMiddleware::class,
        '__construct()' => [
            Reference::to(\League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor::class),
            Reference::to(\hiqdev\yii2\autobus\bus\NearbyHandlerLocator::class),
            Reference::to(\League\Tactician\Handler\MethodNameInflector\HandleInflector::class),
        ],
    ],
    'bus.the-bus' => [
        '__class' => \hiqdev\yii2\autobus\components\TacticianCommandBus::class,
        '__construct()' => [
            Reference::to('bus.default-command-handler'),
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
    \yii\base\Application::class => function () {
        return Yii::$app;
    },
    \yii\mail\MailerInterface::class => function () {
        return Yii::$app->get('mailer');
    },
    \Psr\Log\LoggerInterface::class => function () {
        return Yii::getLogger();
    }
];
