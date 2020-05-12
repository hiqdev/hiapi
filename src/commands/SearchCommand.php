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

use hiapi\validators\LimitValidator;
use hiapi\validators\RefValidator;
use hiqdev\yii\DataMapper\query\Specification;
use hiqdev\yii\DataMapper\query\attributes\validators\WhereValidator;

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
            [
                ['where', 'filter'],
                WhereValidator::class,
                'targetEntityClass' => $this->getEntityClass(),
            ],
            [['page'], 'integer'],
            ['limit', LimitValidator::class],
            [['with', 'include'], 'each', 'rule' => [RefValidator::class]],
            [['count'], 'boolean']
        ];
    }

    /**
     * @var string
     * @psalm-var class-string<Specification>
     */
    protected string $specificationClassName = Specification::class;

    public function getSpecification(): Specification
    {
        $spec = new $this->specificationClassName();
        foreach (['select', 'with', 'where'] as $key) {
            if (!empty($this->{$key})) {
                $spec->{$key} = $this->{$key};
            }
        }
        $spec->limit = $this->limit ?? self::DEFAULT_LIMIT;

        return $spec;
    }
}
