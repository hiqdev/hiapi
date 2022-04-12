<?php

namespace hiapi\middlewares;

use hiapi\commands\error\CommandError;
use League\Tactician\Middleware;
use Psr\Http\Message\ResponseInterface;
use Laminas\Hydrator\HydratorInterface;

class LegacyResponderMiddleware implements Middleware
{
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var HydratorInterface
     */
    private $extractor;

    public function __construct(
        ResponseInterface $response,
        HydratorInterface $extractor
    ) {
        $this->response = $response;
        $this->extractor = $extractor;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $result = $next($command);

        $data = $this->extract($result);

        return $this->createResponseFor($data);
    }

    private function extract($result)
    {
        if ($result instanceof CommandError) {
            return ['_error' => $result->getException()->getMessage()];
        }

        if (is_array($result)) {
            return array_map(function ($item) {
                return $this->extractOne($item);
            }, $result);
        }

        return $this->extractOne($result);
    }

    private function extractOne($result)
    {
        return is_object($result) ? $this->extractor->extract($result) : $result;
    }

    private function createResponseFor($data)
    {
        $response = $this->response
            ->withStatus(200)
            ->withHeader("Content-Type", "application/json");

        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }

        $response->getBody()->write(json_encode($data));

        return $response;
    }
}
