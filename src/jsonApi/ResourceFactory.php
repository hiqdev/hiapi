<?php

namespace hiapi\jsonApi;

use Psr\Container\ContainerInterface;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ResourceDocumentInterface;

/**
 * Creates JsonApi resource for given content.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ResourceFactory implements ResourceFactoryInterface
{
    private ContainerInterface $container;

    private array $resourceMap;

    private array $collections = [];

    private array $resources = [];

    public function __construct(array $resourceMap, ContainerInterface $container)
    {
        $this->resourceMap = $resourceMap;
        $this->container = $container;
    }

    public function getFor($content)
    {
        return is_array($content) ? $this->getCollection($content) : $this->getResource($content);
    }

    /**
     * @param object|string $entity
     */
    public function getResource($entity): ResourceInterface
    {
        $class = is_string($entity) ? $entity : get_class($entity);
        if (empty($this->resources[$class])) {
            $this->resources[$class] = $this->findResource($class, $entity);
        }

        return $this->resources[$class];
    }

    private function findResource(string $class): ResourceInterface
    {
        return $this->container->get($this->findResourceClass($class));
    }

    private function findResourceClass(string $class): string
    {
        if (! empty($this->resourceMap[$class])) {
            return $this->resourceMap[$class];
        }

        $res = $this->findResourceClassByAttribution($class);

        return $res ?: $this->findResourceClassByAncestor($class);

    }

    private function findResourceClassByAttribution($class): ?string
    {
        $result = substr($class, 0, -11) . 'Resource';

        return class_exists($result) ? $result : null;
    }

    private function findResourceClassByAncestor($class): string
    {
        foreach ($this->resourceMap as $ancestor => $result) {
            if (is_subclass_of($class, $ancestor)) {
                return $result;
            }
        }

        throw new \RuntimeException("JsonApi resource not defined for '$class'");
    }

    private function getCollection(array $rows): ResourceDocumentInterface
    {
        if (empty($content)) {
            return new EmptyCollectionDocument;
        }
        $class = get_class(reset($rows));
        if (empty($this->collections[$class])) {
            $this->collections[$class] = $this->findCollection($class);
        }

        return $this->collections[$class];
    }

    private function findCollection(string $class): ResourceDocumentInterface
    {
        $class = $this->resource2collection($this->findResourceClass($class));

        return $this->container->get($class);
    }

    private function resource2collection(string $class): string
    {
        // XXX TMP quick and dirty TODO improve later
        return substr($class, 0, -8) . 'sCollectionDocument';
    }
}
