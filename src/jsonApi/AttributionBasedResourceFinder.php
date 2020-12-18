<?php
declare(strict_types=1);

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;

final class AttributionBasedResourceFinder
{
    private ResourceDocumentFactoryInterface $resourceDocumentFactory;

    public function __construct(ResourceDocumentFactoryInterface $resourceDocumentFactory)
    {
        $this->resourceDocumentFactory = $resourceDocumentFactory;
    }

    public function getResource(string $documentClassName): ResourceInterface
    {
        return $this->resourceDocumentFactory->getResourceByClassName(
            $this->findNearbyAttributionClass($documentClassName)
        );
    }

    private function findNearbyAttributionClass(string $documentClassName): string
    {
        $namespace = implode('\\', array_slice(explode($documentClassName, '\\'), 0, -1));
        $className = basename($documentClassName);
        $entityName = str_replace(['CollectionDocument', 'Document'], '', $className);

        $result = sprintf('%s\%s', $namespace, $entityName);

        return $result;
    }

}
