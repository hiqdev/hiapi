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

use hiqdev\DataMapper\Query\Specification;
use hiqdev\DataMapper\Repository\RepositoryInterface;
use hiqdev\DataMapper\Repository\EntityManagerInterface;

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

    /**
     * @param SearchCommand $command
     */
    public function handle(EntityCommandInterface $command)
    {
        $repo = $this->getRepository($command);
        $specification = $this->buildSpecification($command);

        if ($command->count) {
            return $repo->count($specification);
        }

        return $repo->findAll($specification);
    }

    /**
     * @param EntityCommandInterface|SearchCommand $command
     * @return Specification
     */
    protected function buildSpecification(EntityCommandInterface $command)
    {
        $spec = $this->createSpecification();
        $spec->where(array_merge($command->filter, $command->where));

        if (!$command->count) {
            if (strtolower($command->limit) === 'all') {
                $command->limit = -1;
            }
            $limit = $command->limit ?? 25;
            $spec->limit($limit);
            if ($limit !== -1) {
                $spec->offset((($command->page ?? 0) * $limit) - $limit);
            }
            $spec->with(array_merge($command->include, $command->with));
        }

        return $spec;
    }

    /**
     * @param SearchCommand $command
     * @return RepositoryInterface
     */
    protected function getRepository(EntityCommand $command)
    {
        return $this->em->getRepository($command->getEntityClass());
    }
}
