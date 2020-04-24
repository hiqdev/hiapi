<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use Doctrine\Common\Collections\ArrayCollection;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\Core\Endpoint\EndpointProcessor;
use hiapi\Core\Http\Psr7\Response\FatResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use yii\base\Arrayable;
use yii\base\Model;

class RunEndpointBusMiddleware implements MiddlewareInterface
{
    /**
     * @var EndpointProcessor
     */
    private $endpointProcessor;

    public function __construct(EndpointProcessor $endpointProcessor)
    {
        $this->endpointProcessor = $endpointProcessor;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Model $command */
        $command = $request->getAttribute(CommandForEndpointMiddleware::class);
        /** @var Endpoint $endpoint */
        $endpoint = $request->getAttribute(ResolveEndpointMiddleware::class);

        $result = $this->endpointProcessor->__invoke($command, $endpoint);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $transformedResult = $this->transformResult($result);
        return FatResponse::create($transformedResult, $request);
    }

    /**
     * TODO: Re-implement as a ResourceTransformer, that can serialize Result to an Http\Response
     *       It should be useful to serialize file to StreamResponse, or search result to Reponse with
     *       pagination headers.
     *
     * @param array|ArrayCollection|object|mixed $result
     * @return array|string|null|boolean
     */
    private function transformResult($result)
    {
        if ($result instanceof ArrayCollection) {
            return array_map(function ($item) {
                return $this->transformResult($item);
            }, $result->toArray());
        }

        if ($result instanceof Arrayable) {
            return $result->toArray();
        }

        return $result;
    }
}
