<?php


namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use hiapi\commands\error\AuthenticationError;
use hiapi\commands\error\CommandError;
use League\Tactician\Middleware;
use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use yii\base\InvalidConfigException;
use yii\di\Container;
use yii\web\HttpException;

/**
 * Class JsonApiMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class JsonApiMiddleware implements JsonApiMiddlewareInterface, Middleware
{
    /**
     * @var array
     */
    private $commandToDocumentMap = [];
    /**
     * @var Container
     */
    private $di;
    /**
     * @var JsonApi
     */
    private $jsonApi;

    /**
     * JsonApiMiddleware constructor.
     *
     * @param array $commandToDocumentMap
     * @param Container $di
     * @param JsonApi $jsonApi
     */
    public function __construct(array $commandToDocumentMap, Container $di, JsonApi $jsonApi)
    {
        $this->commandToDocumentMap = $commandToDocumentMap;
        $this->di = $di;
        $this->jsonApi = $jsonApi;
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
     * @return AbstractSuccessfulDocument
     * @throws InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getSuccessDocumentFor($command): AbstractSuccessfulDocument
    {
        $className = get_class($command);
        if (!isset($this->commandToDocumentMap[$className])) {
            throw new InvalidConfigException('Document map for "' . $className . "' does not exist");
        }

        return $this->di->get($this->commandToDocumentMap[$className]);
    }
}
