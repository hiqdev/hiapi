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

use yii\base\Model;

abstract class SearchCommand extends Model
{
    public $select;
    public $where;
    public $limit;

    public function rules()
    {
        return [
            ['select', 'safe'],
            ['where', 'safe'],
            ['limit', 'number', 'max' => 100],
        ];
    }

    abstract public function getEntityClass();
}
