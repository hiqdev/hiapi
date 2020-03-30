<?php

use hiqdev\yii\compat\yii;

return [
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
];
