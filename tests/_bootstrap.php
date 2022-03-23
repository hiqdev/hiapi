<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT & ~E_DEPRECATED);

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    $autoload = __DIR__ . '/../../../autoload.php';
}

require_once $autoload;
require_once dirname($autoload) . '/yiisoft/yii2/Yii.php';

use yii\console\Application;
use Yiisoft\Composer\Config\Builder;

Yii::setAlias('@root', dirname(__DIR__));
Yii::$app = new Application(require Builder::path('common'));

