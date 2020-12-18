<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use Psr\Container\ContainerInterface;
use RuntimeException;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ResourceDocumentInterface;

/**
 * Creates JsonApi resource document for given content.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ResourceDocumentFactory implements ResourceDocumentFactoryInterface
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

    /** {@inheritDoc} */
    public function getResourceDocumentFor($content): ResourceDocumentInterface
    {
        if (is_array($content)) {
            return $this->getCollectionDocument($content);
        }

        return $this->getSingleDocument($content);
    }

    /** {@inheritDoc} */
    public function getResourceByClassName(string $entityClassName): ResourceInterface
    {
        if (empty($this->resources[$entityClassName])) {
            $this->resources[$entityClassName] = $this->container->get(
                $this->findResourceClass($entityClassName)
            );
        }

        return $this->resources[$entityClassName];
    }

    /** {@inheritDoc} */
    public function getResourceFor(object $entity): ResourceInterface
    {
        $class = get_class($entity);

        return $this->getResourceByClassName($class);
    }

    private function findResourceClass(string $class): string
    {
        if (! empty($this->resourceMap[$class])) {
            return $this->resourceMap[$class];
        }

        $res = $this->findResourceClassByAttribution($class);

        return $res ?? $this->findResourceClassByAncestor($class);

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

        throw new RuntimeException("JsonApi resource not defined for '$class'");
    }

    private function getCollectionDocument(array $rows): ResourceDocumentInterface
    {
        if (empty($rows)) {
            return new EmptyCollectionDocument();
        }
        $class = get_class(reset($rows));
        if (empty($this->collections[$class])) {
            $this->collections[$class] = $this->findCollection($class);
        }

        return $this->collections[$class];
    }

    private function getSingleDocument(object $entity): ResourceDocumentInterface
    {
        $singleDocumentClass = $this->resource2singleDocument(
            $this->findResourceClass(get_class($entity))
        );

        return $this->container->get($singleDocumentClass);
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

    private function resource2singleDocument(string $class): string
    {
        // XXX TMP quick and dirty TODO improve later
        return substr($class, 0, -8) . 'Document';
    }
}
