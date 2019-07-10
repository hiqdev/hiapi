<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiapi\commands;

use Psr\Http\Message\ServerRequestInterface;

abstract class BaseCommand extends \yii\base\Model
{
    public function loadFromServerRequest(ServerRequestInterface $request): bool
    {
        $data = $request->getParsedBody() ?: $request->getQueryParams();

        return $this->load($data, '');
    }

    public function commandName(): string
    {
        return strtr((new \ReflectionObject($this))->getShortName(), [
            'Command' => '',
        ]);
    }
}
