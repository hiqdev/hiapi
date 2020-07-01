<?php

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ResourceDocumentInterface;

/**
 * Creates JsonApi resource for given content.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ResourceFactoryInterface
{
    /**
     * @return ResourceInterface|ResourceDocumentInterface
     */
    public function getFor($content);
}
