<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use hiapi\jsonApi\ResourceFactoryInterface;
use hiqdev\DataMapper\Attribution\AttributionInterface;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

abstract class AttributionBasedResource extends AbstractResource
{
    protected string $attributionClass;

    protected AttributionInterface $attribution;

    protected ResourceFactoryInterface $resourceFactory;

    public function __construct(ResourceFactoryInterface $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function getType($entity): string
    {
        return lcfirst(substr(strrchr(get_class($this), '\\'), 1, -8));
    }

    public function getId($entity): string
    {
        return (string)$entity->getId();
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
        $res = [];
        foreach ($this->getAttribution()->attributes() as $key => $type) {
            if ($key === 'id') {
                continue;
            }
            $method = 'get' . ucfirst($key);
            if (! method_exists($entity, $method)) {
                continue;
            }
            $res[$key] = fn($po): ?string => $po->{$method}();
        }

        return $res;
    }

    public function getDefaultIncludedRelationships($entity): array
    {
        return array_keys($this->getAttribution()->relations());
    }

    public function getRelationships($entity): array
    {
        $res = [];
        foreach ($this->getAttribution()->relations() as $key => $type) {
            $method = 'get' . ucfirst($key);
            if (! method_exists($entity, $method)) {
                continue;
            }
            $res[$key] = fn($po) => ToOneRelationship::create()
                ->setData($po->{$method}(), $this->getResourceFor($type))
                ->omitDataWhenNotIncluded()
            ;
        }

        return $res;
    }

    public function getAttribution(): AttributionInterface
    {
        if (! isset($this->attribution)) {
            $this->attribution = new $this->attributionClass();
        }

        return $this->attribution;
    }

    public function getResourceFor($entity)
    {
        return $this->resourceFactory->getFor($entity);
    }
}
