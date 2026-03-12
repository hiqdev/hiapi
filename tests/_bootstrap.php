<?php

use yii\web\Application;
use Yiisoft\Composer\Config\Builder;

const APP_TYPE = 'tests';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$config = require Builder::path('web');

$autoload = __DIR__ . '/../vendor/autoload.php';
$root = dirname(__DIR__);
if (!file_exists($autoload)) {
    $autoload = __DIR__ . '/../../../autoload.php';
    $root = dirname(__DIR__, 4);
}

require_once $autoload;
// Register test namespace when running from project root where autoload-dev is unavailable
spl_autoload_register(static function (string $class) {
    $prefix = 'hiapi\\tests\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
require_once dirname($autoload) . '/yiisoft/yii2/Yii.php';

Yii::setAlias('@root', $root);
Yii::$app = new Application($config);
