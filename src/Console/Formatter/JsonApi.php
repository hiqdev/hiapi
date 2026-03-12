<?php

namespace hiapi\Core\Console\Formatter;

use hiapi\jsonApi\ResourceDocumentFactoryInterface;
use Lcobucci\ContentNegotiation\Formatter;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WoohooLabs\Yin\JsonApi\JsonApi as Yin;

final readonly class JsonApi implements Formatter
{
    public function __construct(private ResourceDocumentFactoryInterface $resourceFactory, private Yin $jsonApi)
    {
    }

    public function format(UnformattedResponse $response, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        $content = $response->getUnformattedContent();
        $document = $this->resourceFactory->getResourceDocumentFor($content);

        return $this->jsonApi->respond()->ok($document, $content);
    }
}
