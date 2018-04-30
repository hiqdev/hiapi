<?php

namespace hiapi\middlewares;

use hiapi\commands\error\CommandError;
use League\Tactician\Middleware;
use Psr\Http\Message\ResponseInterface;
use Zend\Hydrator\HydratorInterface;

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
        $handledCommand = $next($command);

        if ($handledCommand instanceof CommandError) {
            $data = ['_error' => $handledCommand->getException()->getMessage()];
        } elseif (is_array($handledCommand)) {
            $data = array_map(function ($item) {
                return $this->extractor->extract($item);
            }, $handledCommand);
        } else {
            $data = $this->extractor->extract($handledCommand);
        }

        return $this->createResponseFor($data);
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
