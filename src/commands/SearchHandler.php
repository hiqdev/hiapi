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
use transmedia\hiapi\modules\client\jsonApi\ClientResourceTransformer;
use transmedia\hiapi\modules\client\jsonApi\ClientsCollectionDocument;
use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use WoohooLabs\Yin\JsonApi\JsonApi;

class SearchHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var JsonApi
     */
    private $jsonApi;

    public function __construct(EntityManagerInterface $em, JsonApi $jsonApi)
    {
        $this->em = $em;
        $this->jsonApi = $jsonApi;
    }

    /**
     * @return Specification
     */
    protected function createSpecification()
    {
        return new Specification();
    }

    public function handle(SearchCommand $command)
    {
        $response = $this->jsonApi->respond();

        $results = $this->getRepository($command)->findAll($this->buildSpecification($command));

        return $response->ok(
            new ClientsCollectionDocument(new ClientResourceTransformer()),
            $results
        );
    }

    protected function buildSpecification(SearchCommand $command)
    {
        return $this->createSpecification()->where($command->where)->limit($command->limit ?: 25);
    }

    /**
     * @param SearchCommand $command
     * @return BaseRepository
     */
    protected function getRepository(SearchCommand $command)
    {
        return $this->em->getRepository($command->getEntityClass());
    }
}
