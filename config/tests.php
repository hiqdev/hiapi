<?php

use hiqdev\yii\compat\yii;
use hiqdev\yii\compat\Buildtime;

$config = require 'common.php';

$logComponent = Buildtime::run(yii::is3()) ? 'logger' : 'log';

// Disable logging
$config['components'][$logComponent]['enabled'] = false;
$config['components'][$logComponent]['targets']['file']['enabled'] = false;

return $config;