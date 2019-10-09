<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

$app = [
    'controllerMap' => [
        'api' => [
            '__class' => \hiapi\console\ApiController::class,
        ],
        'hiapi' => [
            '__class' => \hiapi\console\HiapiController::class,
        ],
        'queue' => [
            '__class' => \hiapi\console\QueueController::class,
        ],
    ],
];

$singletons = [
    \yii\web\User::class => [
        'identity' => \hiqdev\yii\compat\yii::referenceTo('console.default-user'),
    ],
    'console.default-user' => [
        '__class' => \hiapi\models\HiamUserIdentity::class,
        'id' => 1,
        'login' => 'console_user',
    ],
/// BUS
    'bus.responder-middleware'          => \hiqdev\yii2\autobus\bus\BypassMiddleware::class,
    'bus.handle-exceptions-middleware'  => \hiqdev\yii2\autobus\bus\BypassMiddleware::class,
    'bus.loader-middleware'             => \hiqdev\yii2\autobus\bus\BypassMiddleware::class,
];

return \hiqdev\yii\compat\yii::is2() ? array_merge([
    'container' => [
        'singletons' => $singletons,
    ],
    'logger' => [
        '__class' => \yii\log\Logger::class,
        'flushInterval' => 1,
        'targets' => [
            [
                '__class' => \hiapi\console\ConsoleLogTarget::class,
                'exportContext' => YII_ENV === 'dev' ? [
                    Psr\Log\LogLevel::EMERGENCY => true,
                    Psr\Log\LogLevel::ERROR     => true,
                    Psr\Log\LogLevel::ALERT     => true,
                    Psr\Log\LogLevel::CRITICAL  => true,
                    Psr\Log\LogLevel::WARNING   => true,
                    Psr\Log\LogLevel::NOTICE    => true,
                    Psr\Log\LogLevel::INFO      => true,
                    Psr\Log\LogLevel::DEBUG     => true,
                ] : [
                    Psr\Log\LogLevel::EMERGENCY => false,
                    Psr\Log\LogLevel::ERROR     => false,
                    Psr\Log\LogLevel::ALERT     => false,
                    Psr\Log\LogLevel::CRITICAL  => false,
                    Psr\Log\LogLevel::WARNING   => false,
                ],
                'styles' => YII_ENV === 'dev' ? [
                    Psr\Log\LogLevel::EMERGENCY => [yii\helpers\Console::BOLD, yii\helpers\Console::BG_RED],
                    Psr\Log\LogLevel::ERROR     => [yii\helpers\Console::FG_RED, yii\helpers\Console::BOLD],
                    Psr\Log\LogLevel::ALERT     => [yii\helpers\Console::FG_RED],
                    Psr\Log\LogLevel::CRITICAL  => [yii\helpers\Console::FG_RED],
                    Psr\Log\LogLevel::WARNING   => [yii\helpers\Console::FG_YELLOW],
                    Psr\Log\LogLevel::NOTICE    => [],
                    Psr\Log\LogLevel::INFO      => [],
                    Psr\Log\LogLevel::DEBUG     => [yii\helpers\Console::FG_GREEN],
                ] : [
                    Psr\Log\LogLevel::EMERGENCY => [yii\helpers\Console::BOLD, yii\helpers\Console::BG_RED],
                    Psr\Log\LogLevel::ERROR     => [yii\helpers\Console::FG_RED, yii\helpers\Console::BOLD],
                    Psr\Log\LogLevel::ALERT     => [yii\helpers\Console::FG_RED],
                    Psr\Log\LogLevel::CRITICAL  => [yii\helpers\Console::FG_RED],
                    Psr\Log\LogLevel::WARNING   => [yii\helpers\Console::FG_YELLOW],
                ],
            ],
        ]
    ],
], $app) : array_merge([
    'app' => $app,
], $singletons);
