<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

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
            'class' => \yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
        ],
    ],
    'modules' => array_filter([
        'debug' => empty($params['debug.enabled']) ? null : array_filter([
            'class' => \yii\debug\Module::class,
            'allowedIPs' => isset($params['debug.allowedIps']) ? $params['debug.allowedIps'] : null,
            'historySize' => isset($params['debug.historySize']) ? $params['debug.historySize'] : null,
        ]),
    ]),
    'container' => [
        'singletons' => [
            \yii\base\Application::class => function () {
                return Yii::$app;
            },
            \yii\mail\MailerInterface::class => function () {
                return Yii::$app->get('mailer');
            },
        ],
    ],
]);
