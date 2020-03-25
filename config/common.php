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
];

$singletons = [
    \yii\web\User::class => [
        'identityClass' => \hiapi\Core\Auth\UserIdentity::class,
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

/// Middlewares
    \hiapi\Core\Http\Psr15\RequestHandler::class => [
        '__construct()' => [
            'quiet'         => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\QuietMiddleware::class),
            'content-type'  => yii::referenceTo(\Lcobucci\ContentNegotiation\ContentTypeMiddleware::class),
            'exception'     => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\ExceptionMiddleware::class),
            'blacklist'     => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\BlacklistMiddleware::class),
            'user-real-ip'  => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\UserRealIpMiddleware::class),
            'auth'          => yii::referenceTo(\hiapi\Core\Auth\AuthMiddleware::class),
            'cors'          => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\CorsMiddleware::class),
            'perform'       => yii::referenceTo('hiapi-endpoint-middleware'),
            new \hiqdev\composer\config\utils\RemoveArrayKeys(),
        ],
    ],

    'hiapi-endpoint-middleware' => [
        '__class' => \hiapi\Core\Http\Psr15\Middleware\RelayMiddleware::class,
        '__construct()' => [
            'resolve'       => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\ResolveEndpointMiddleware::class),
            'build-command' => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\CommandForEndpointMiddleware::class),
            'run'           => yii::referenceTo(\hiapi\Core\Http\Psr15\Middleware\RunEndpointBusMiddleware::class),
            new \hiqdev\composer\config\utils\RemoveArrayKeys(),
        ],
    ],

    \Lcobucci\ContentNegotiation\ContentTypeMiddleware::class => [
        '__construct()' => [
            yii::referenceTo('content-types'),
            [
                'text/json'             => new \hiapi\Core\Console\Formatter\Json(),
                'application/json'      => new \hiapi\Core\Console\Formatter\Json(),
                'application/x-json'    => new \hiapi\Core\Console\Formatter\Json(),
                'text/plain'            => new \hiapi\Core\Console\Formatter\Text(),
                'text/php'              => new \hiapi\Core\Console\Formatter\Php(),
                'text/x-php'            => new \hiapi\Core\Console\Formatter\Php(),
                'application/php'       => new \hiapi\Core\Console\Formatter\Php(),
            ],
            new \Laminas\Diactoros\StreamFactory(),
        ],
    ],
    'content-types' => [
        '__class' => \Middlewares\ContentType::class,
        '__construct()' => [
            [
                'json' => [
                    'extension' => ['json'],
                    'mime-type' => ['application/json', 'application/x-json', 'text/json'],
                    'charset' => true,
                ],
                'text' => [
                    'extension' => ['txt'],
                    'mime-type' => ['text/plain'],
                    'charset' => true,
                ],
                'php' => [
                    'extension' => ['php'],
                    'mime-type' => ['text/php', 'text/x-php', 'application/php'],
                    'charset' => true,
                ],
            ],
        ],
    ],

    \hiapi\Core\Http\Psr15\Middleware\UserRealIpMiddleware::class => [
        '__construct()' => [
            $params['hiapi.trustedRemoteNetworks'],
        ],
    ],

    \hiapi\Core\Http\Psr15\Middleware\BlacklistMiddleware::class => [
        '__construct()' => [
            $params['hiapi.BlacklistMiddleware.restriction'],
        ],
    ],

    \hiapi\Core\Auth\AuthMiddleware::class =>
        yii::referenceTo(\hiapi\Core\Auth\OAuth2Middleware::class),

    \hiapi\Core\Auth\OAuth2Middleware::class => [
        'userinfoUrl' => $params['hiapi.oauth2.userinfoUrl'],
    ],

    \hiapi\Core\Endpoint\EndpointRepository::class => [
        '__class' => \hiapi\Core\Endpoint\EndpointRepository::class,
        '__construct()' => [
            new \hiqdev\composer\config\utils\RemoveArrayKeys(),
        ],
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
        return \hiqdev\yii\compat\yii::getApp()->get('mailer');
    },
];

return yii::is3() ? array_merge([
    'aliases' => $aliases,
    'app' => $app,
], $components, $singletons) : array_merge([
    'bootstrap' => ['log'],
    'aliases' => $aliases,
    'components' => $components,
    'container' => [
        'singletons' => $singletons,
    ],
    'params' => $params,
    'vendorPath' => '@root/vendor',
    'runtimePath' => '@root/runtime',
], $app);
