<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiapi\bus;

use hiqdev\yii2\autobus\components\BranchedAutoBus;
use hiqdev\yii2\autobus\components\CommandFactoryInterface;

class ApiCommandsBus extends BranchedAutoBus implements ApiCommandsBusInterface
{
}
