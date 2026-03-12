<?php


namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\commands\error\CommandError;
use hiapi\commands\SearchCommand;
use hiapi\jsonApi\SearchCountDocument;
use League\Tactician\Middleware;
use Psr\Container\ContainerInterface;
use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Schema\Error;

/**
 * Class JsonApiMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class JsonApiMiddleware implements JsonApiMiddlewareInterface, Middleware
{
    /**
     * JsonApiMiddleware constructor.
     *
     * @param array $commandToDocumentMap
     * @param ContainerInterface $di
     * @param JsonApi $jsonApi
     */
    public function __construct(private array $commandToDocumentMap, private readonly ContainerInterface $di, private readonly JsonApi $jsonApi)
    {
    }

    public function execute($command, callable $next)
    {
        $response = $this->jsonApi->respond();
        $result = $next($command);

        if ($result instanceof CommandError) {
            return $response->genericError(new ErrorDocument(), [
                Error::create()
                    ->setTitle($result->getException()->getMessage())
                    ->setMeta($result->getCommand()->getAttributes())
                ,
            ], $result->getStatusCode());
        }

        return $response->ok($this->getSuccessDocumentFor($command), $result);
    }

    /**
     * @param BaseCommand $command
     * @return AbstractSuccessfulDocument|\WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument
     */
    public function getSuccessDocumentFor($command)
    {
        $className = $command::class;
        if (!isset($this->commandToDocumentMap[$className])) {
            throw new \OutOfRangeException('Document map for "' . $className . "' does not exist");
        }
        if ($command instanceof SearchCommand && $command->count) {
            return $this->di->get(SearchCountDocument::class);
        }

        return $this->di->get($this->commandToDocumentMap[$className]);
    }
}
