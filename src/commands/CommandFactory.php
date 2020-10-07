<?php
declare(strict_types=1);

namespace hiapi\Core\commands;

use hiapi\commands\BulkCommand;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use hiapi\exceptions\ConfigurationException;
use hiapi\exceptions\HiapiException;
use Psr\Http\Message\ServerRequestInterface;
use yii\base\Model;

/**
 * Creates command by given data: name and request.
 * - takes data from POST or (if it is empty) from GET request,
 * - trims all the values and tries to load them to the command.
 */
class CommandFactory
{
    public function createByEndpoint(Endpoint $endpoint, ServerRequestInterface $request): Model
    {
        $data = $this->extractData($request);
        $command = $this->createCommand($endpoint);
        $successLoad = $command->load($data, '');
        if (!$successLoad && !empty($data)) {
            throw new HiapiException('Failed to load command data');
        }

        return $command;
    }

    private function createCommand(Endpoint $endpoint): Model
    {
        $inputType = $endpoint->inputType;
        if ($inputType instanceof Collection) {
            $command = BulkCommand::of($inputType->getEntriesClass());
        } else {
            $command = new $inputType();
        }
        if (!$command instanceof Model) {
            throw new ConfigurationException('This middleware can load only commands of Model class');
        }

        return $command;
    }

    private function extractData(ServerRequestInterface $request): array
    {
        $data = array_merge($request->getParsedBody(), $request->getQueryParams());
        array_walk_recursive($data, static function (&$value) {
            if (\is_string($value)) {
                $value = trim($value);
            }
        });

        return $data;
    }
}
