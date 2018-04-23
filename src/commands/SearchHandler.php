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
 * Class SearchHandler
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SearchHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SearchHandler constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Specification
     */
    protected function createSpecification()
    {
        return new Specification();
    }

    public function handle(EntityCommandInterface $command)
    {
        return $this->getRepository($command)->findAll($this->buildSpecification($command));
    }

    /**
     * @param EntityCommandInterface|SearchCommand $command
     * @return Specification
     */
    protected function buildSpecification(EntityCommandInterface $command)
    {
        $spec = $this->createSpecification();
        $spec->where(array_merge($command->filter, $command->where));
        $spec->limit($command->limit ?: 25);
        $spec->with(array_merge($command->include, $command->with));
        
        return $spec;
    }

    /**
     * @param SearchCommand $command
     * @return BaseRepository
     */
    protected function getRepository(EntityCommand $command)
    {
        return $this->em->getRepository($command->getEntityClass());
    }
}
