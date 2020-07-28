<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

final class EmptyResource extends AbstractResource
{
    public function getType($entity): string
    {
        return 'empty';
    }

    public function getId($entity): string
    {
        return '';
    }

    public function getMeta($entity): array
    {
        return [];
    }

    public function getLinks($entity): ?ResourceLinks
    {
        return null;
    }

    public function getAttributes($entity): array
    {
        return [];
    }

    public function getDefaultIncludedRelationships($entity): array
    {
        return [];
    }

    public function getRelationships($entity): array
    {
        return [];
    }
}
