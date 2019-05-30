<?php

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Data\SingleResourceData;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Transformer\Transformation;

/**
 * Class NewResourceCreatedDocument satisfies Yin 3.1
 *
 * @deprecated TEMP, TO BE DROPPED. DO NOT USE!
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class NewResourceCreatedDocument extends AbstractSuccessfulDocument
{
    use JsonApiObjectProviderTrait;

    /**
     * @var ResourceInterface
     */
    protected $transformer;

    public function __construct(ResourceInterface $transformer)
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
