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

use hiapi\validators\RefValidator;

abstract class SearchCommand extends BaseCommand
{
    public $select;
    public $where;
    public $limit;
    public $with = [];

    public function rules()
    {
        return [
            ['select', 'safe'],
            ['where', 'safe'],
            ['limit', 'number', 'max' => 100],
            ['with', 'each', 'rule' => [RefValidator::class]],
        ];
    }

    abstract public function getEntityClass();
}
