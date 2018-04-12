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

/**
 * Class SearchCommand
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class SearchCommand extends EntityCommand
{
    public $select;
    public $limit;
    public $where = [];
    public $filter = [];
    public $with = [];
    public $include = [];

    public function rules()
    {
        return [
            ['select', 'safe'],
            [['where', 'filter'], 'safe'],
            ['limit', 'number', 'max' => 100],
            [['with', 'include'], 'each', 'rule' => [RefValidator::class]],
        ];
    }
}
