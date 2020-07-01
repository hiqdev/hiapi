<?php

namespace hiapi\Core\Console\Formatter;

use hiapi\jsonApi\ResourceFactoryInterface;
use Lcobucci\ContentNegotiation\Formatter;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WoohooLabs\Yin\JsonApi\JsonApi as Yin;

final class JsonApi implements Formatter
{
    private ResourceFactoryInterface $resourceFactory;

    private Yin $jsonApi;

    public function __construct(ResourceFactoryInterface $resourceFactory, Yin $jsonApi)
    {
        $this->resourceFactory = $resourceFactory;
        $this->jsonApi = $jsonApi;
    }

    public function format(UnformattedResponse $response, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        $content = $response->getUnformattedContent();
        $document = $this->resourceFactory->getFor($content);

        return $this->jsonApi->respond()->ok($document, $content);
    }
}
