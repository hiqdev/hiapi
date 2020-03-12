<?php
namespace hiapi\Core\Http\Psr15\Middleware;

use Exception;
use hiapi\commands\BulkCommand;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use yii\base\Model;

/**
 * Class CommandForEndpointMiddleware takes data from POST or (if it is empty) from GET request,
 * trims all the values and tries to load them to the command.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CommandForEndpointMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = array_merge($request->getParsedBody() ?: $request->getQueryParams());
        array_walk_recursive($data, static function (&$value) {
            if (\is_string($value)) {
                $value = trim($value);
            }
        });

        $command = $this->createCommand($request);
        $successLoad = $command->load($data, '');
        if (!$successLoad && !empty($data)) {
            // TODO: specific exception
            throw new Exception('Failed to load command');
        }

        return $handler->handle(
            $request->withAttribute(self::class, $command)
        );
    }

    private function createCommand(ServerRequestInterface $request): Model
    {
        /** @var Endpoint $endpoint */
        $endpoint = $request->getAttribute(ResolveEndpointMiddleware::class);

        $inputType = $endpoint->getInputType();
        if ($inputType instanceof Collection) {
            $command = BulkCommand::of($inputType->getEntriesClass());
        } else {
            $command = new $inputType();
        }
        if (!$command instanceof Model) {
            // TODO: specific exception
            throw new Exception('This middleware can load only commands of Model class');
        }

        return $command;
    }
}
