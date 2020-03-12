<?php

namespace hiapi\Core\Http\Psr15\Middleware;

use hiapi\Core\Http\Psr7\Response\FatResponse;
use hiapi\exceptions\NotAuthenticatedException;
use hiapi\legacy\lib\mrdpBase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UseBaseMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->perform($request);
    }

    private function perform(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->getCommandName($request);

        $this->ensureCommandExists($command);
        $this->ensureCommandAllowed($command);

        $data = $this->getInputData($request);

        $res = $this->getBase()->{$command}($data);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        return FatResponse::create($res, $request);
    }

    private function getInputData(ServerRequestInterface $request): array
    {
        $query = $request->getQueryParams();
        $post  = $request->getParsedBody();

        return array_merge($query, $post);

        // XXX TODO check if it really exactly corresponds to
        // return $_REQUEST;
    }

    private function getCommandName(MessageInterface $request): string
    {
        $path = $request->getUri()->getPath();
        $URLPARTS = explode('/', ltrim($path, '/'));

        return $URLPARTS[0];
    }

    private $base;

    private function getBase(): mrdpBase
    {
        if ($this->base === null) {
            $this->base = $this->container->get(mrdpBase::class);
        }

        return $this->base;
    }

    private function ensureCommandExists(string $command): void
    {
        if (!$this->getBase()->hasCommand($command)) {
            throw new \RuntimeException("Not existing command: $command");
        }
    }

    private function ensureCommandAllowed(string $command): void
    {
        if (!$this->getBase()->isCommandAllowed($command)) {
            throw new NotAuthenticatedException("Not allowed command: $command");
        }
    }
}
