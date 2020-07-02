<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use hiapi\jsonApi\ResourceFactoryInterface;

/**
 * Allows easy creation of JSON:API collection documents class.
 * `attributionClass` can be provided or it will be found automatically
 * by class name in the same directory, see `findAttributionClass`.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class AttributionBasedCollectionDocument extends AbstractCollectionDocument
{
    protected string $attributionClass;

    public function __construct(ResourceFactoryInterface $resourceFactory)
    {
        parent::__construct($resourceFactory->getFor($this->getAttributionClass()));
    }

    public function getAttributionClass(): string
    {
        if (empty($this->attributionClass)) {
            $this->attributionClass = $this->findAttributionClass();
        }

        return $this->attributionClass;
    }

    private function findAttributionClass(): string
    {
        return substr(get_class($this), 0, -19) . 'Attribution';
    }

    public function getJsonApi(): ?JsonApiObject
    {
        return new JsonApiObject('1.1');
    }

    public function getMeta(): array
    {
        return [];
    }

    public function getLinks(): ?DocumentLinks
    {
        return null;
    }
}
