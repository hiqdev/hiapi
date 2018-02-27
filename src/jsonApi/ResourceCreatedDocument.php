<?php

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Data\SingleResourceData;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Transformer\AbstractResourceTransformer;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformerInterface;
use WoohooLabs\Yin\JsonApi\Transformer\Transformation;

/**
 * Class ResourceCreatedDocument
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ResourceCreatedDocument extends AbstractSuccessfulDocument
{
    use JsonApiObjectProviderTrait;

    /**
     * @var AbstractResourceTransformer|ResourceTransformerInterface
     */
    protected $transformer;

    public function __construct(ResourceTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function createData(): DataInterface
    {
        return new SingleResourceData();
    }

    /**
     * {@inheritdoc}
     */
    protected function fillData(Transformation $transformation): void
    {
        $transformation->data->addPrimaryResource($this->transformer->transformToResource($transformation, $this->domainObject));
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipMember(
        string $relationshipName,
        Transformation $transformation,
        array $additionalMeta = []
    ): ?array {
        return null;
    }
}
