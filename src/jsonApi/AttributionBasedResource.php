<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use hiapi\jsonApi\ResourceDocumentFactoryInterface;
use hiqdev\DataMapper\Attribute\DateTimeAttribute;
use hiqdev\DataMapper\Attribution\AttributionInterface;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;

abstract class AttributionBasedResource extends AbstractResource
{
    protected string $attributionClass;

    protected AttributionInterface $attribution;

    protected ResourceDocumentFactoryInterface $resourceFactory;

    public function __construct(ResourceDocumentFactoryInterface $resourceFactory)
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
            foreach ([$key, 'get' . ucfirst($key)] as $method) {
                if (method_exists($entity, $method)) {
                    $res[$key] = $this->buildAttributeMethod($method, $type);
                    break;
                }
            }
        }

        return $res;
    }

    protected function buildAttributeMethod(string $method, $type): callable
    {
        if ($type === DateTimeAttribute::class) {
            return static function ($po) use ($method): ?string {
                $value = $po->{$method}();
                if ($value === null) {
                    return null;
                }

                return $value->format(DATE_ATOM);
            };
        }

        return fn($po): ?string => $po->{$method}();
    }

    public function getDefaultIncludedRelationships($entity): array
    {
        return array_keys($this->getAttribution()->relations());
    }

    public function getRelationships($entity): array
    {
        $res = [];
        foreach ($this->getAttribution()->relations() as $key => $type) {
            foreach ([$key, 'get' . ucfirst($key)] as $method) {
                if (method_exists($entity, $method)) {
                    $res[$key] = fn($po) => ToOneRelationship::create()
                        ->setData($po->{$method}(), $this->getResourceFor($type))
                        ->omitDataWhenNotIncluded();
                    break;
                }
            }
        }

        return $res;
    }

    public function getAttribution(): AttributionInterface
    {
        if (! isset($this->attribution)) {
            $class = $this->getAttributionClass();
            $this->attribution = new $class();
        }

        return $this->attribution;
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
        return substr(get_class($this), 0, -8) . 'Attribution';
    }

    public function getResourceFor($className): ResourceInterface
    {
        return $this->resourceFactory->getResourceByClassName($className);
    }
}
