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
use hiqdev\yii\DataMapper\query\Specification;

/**
 * Class SearchCommand
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class SearchCommand extends EntityCommand
{
    public $select;
    public $limit;
    public $page;
    public $where = [];
    public $filter = [];
    public $with = [];
    public $include = [];
    public $count;

    const DEFAULT_LIMIT = 25;

    public function rules()
    {
        return [
            ['select', 'safe'],
            [['where', 'filter'], 'safe'],
            [['page'], 'integer'],
            ['limit', function () {
                if (empty($this->limit)) {
                    return;
                }

                if (mb_strtolower($this->limit) === 'all') {
                    $this->limit = 'all';
                    return;
                }

                if ($this->limit < 1 || $this->limit > 1000) {
                    $this->addError('limit', 'Limit must be between 1 and 1000');
                    return;
                }
            }],
            [['with', 'include'], 'each', 'rule' => [RefValidator::class]],
            [['count'], 'boolean']
        ];
    }

    public function getSpecification(): Specification
    {
        $spec = new Specification();
        foreach (['select', 'limit', 'with', 'where'] as $key) {
            if (!empty($this->{$key})) {
                $spec->{$key} = $this->{$key};
            }
        }
        if (empty($spec->limit)) {
            $spec->limit = self::DEFAULT_LIMIT;
        }

        return $spec;
    }
}
