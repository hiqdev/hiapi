<?php

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\RequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Data\SingleResourceData;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Transformer\AbstractResourceTransformer;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformerInterface;
use WoohooLabs\Yin\JsonApi\Transformer\Transformation;

/**
 * Class SearchCountDocument
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SearchCountDocument extends AbstractSuccessfulDocument
{
    use JsonApiObjectProviderTrait;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta(): array
    {
        return ['count' => $this->domainObject];
    }

    public function getContent(
        RequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        $domainObject,
        array $additionalMeta = []
    ): array {
        return array_merge(parent::getContent($request, $exceptionFactory, $domainObject, $additionalMeta), [
            'data' => [$this->getMeta()]
        ]);
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
