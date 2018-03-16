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

interface EntityCommandInterface
{
    /**
     * Returns entity class. E.g. My\Domain\Entity::class
     * @return string
     */
    public function getEntityClass(): string;
}
