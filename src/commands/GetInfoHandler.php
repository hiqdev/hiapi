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

use hiqdev\yii\DataMapper\components\EntityManagerInterface;
use hiqdev\yii\DataMapper\query\Specification;
use hiqdev\yii\DataMapper\repositories\BaseRepository;

/**
 * Class GetInfoHandler
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class GetInfoHandler extends SearchHandler
{
    public function handle(EntityCommandInterface $command)
    {
        return $this->getRepository($command)->findOneOrFail($this->buildSpecification($command));
    }

    protected function buildSpecification(EntityCommandInterface $command)
    {
        return $this->createSpecification()->where(['id' => $command->id])->limit(1);
    }
}
