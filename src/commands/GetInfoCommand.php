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

abstract class GetInfoCommand extends EntityCommand
{
    public $id;

    public function getId()
    {
        return $this->id;
    }

    public function rules()
    {
        return [
            ['id', 'integer', 'min' => 1],
            ['id', 'required'],
        ];
    }
}
