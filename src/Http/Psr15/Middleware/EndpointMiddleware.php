<?php
declare(strict_types=1);

namespace hiapi\Core\Http\Psr15\Middleware;

use Doctrine\Common\Collections\ArrayCollection;
use hiapi\Core\commands\CommandFactory;
use hiapi\Core\Endpoint\EndpointProcessor;
use hiapi\Core\Endpoint\EndpointRepository;
use hiapi\Core\Http\Psr7\Response\FatResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use yii\base\Arrayable;

/**
 * Complete endpoint execution:
 * - resolve endpoint with EndpointRepository
 * - create command with CommandFactory
 * - run endpoint with EndpointProcessor
 * The middleware can be bound to single command with `endpointName`.
 */
class EndpointMiddleware implements MiddlewareInterface
{
    private string $endpointName;
    private CommandFactory $commandFactory;
    private EndpointRepository $endpointRepository;
    private EndpointProcessor $endpointProcessor;

    public function __construct(
        string $enpointName = '',
        CommandFactory $commandFactory,
        EndpointRepository $endpointRepository,
        EndpointProcessor $endpointProcessor
    ) {
        $this->endpointName = $enpointName;
        $this->commandFactory = $commandFactory;
        $this->endpointRepository = $endpointRepository;
        $this->endpointProcessor = $endpointProcessor;
    }

    public static function for(string $name): \Closure
    {
        return function (
            ServerRequestInterface $request, RequestHandlerInterface $handler,
            CommandFactory $commandFactory,
            EndpointRepository $endpointRepository,
            EndpointProcessor $endpointProcessor
        ) use ($name) {
            $middleware = new self($name, $commandFactory, $endpointRepository, $endpointProcessor);

            return $middleware->process($request, $handler);
        };
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $endpoint = $this->endpointRepository->getByName($this->getName($request));
        $command = $this->commandFactory->createByEndpoint($endpoint, $request);
        $result = $this->endpointProcessor->__invoke($command, $endpoint);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return FatResponse::create($this->transformResult($result), $request);
    }

    private function getName(ServerRequestInterface $request): string
    {
        return $this->endpointName ?: trim($request->getUri()->getPath(), '/');
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
