<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * Allows easy creation of JSON:API documents class.
 * `attributionClass` can be provided or it will be found automatically
 * by class name in the same directory, see `findAttributionClass`.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class AttributionBasedSingleResourceDocument extends AbstractSingleResourceDocument
{
    public function __construct(AttributionBasedResourceFinder $resourceFinder)
    {
        $resource = $resourceFinder->getResource(static::class);

        parent::__construct($resource);
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
