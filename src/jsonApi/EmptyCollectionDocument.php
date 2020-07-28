<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * Empty collection to return empty array (nothin is found).
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
final class EmptyCollectionDocument extends AbstractCollectionDocument
{
    public function __construct()
    {
        parent::__construct(new EmptyResource);
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
