<?php


namespace hiapi\middlewares;

use hiapi\commands\BaseCommand;
use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;
use yii\base\InvalidConfigException;

/**
 * Interface JsonApiMiddlewareInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface JsonApiMiddlewareInterface
{
    /**
     * @param BaseCommand $command
     * @return AbstractSuccessfulDocument|\WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument
     * @throws InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getSuccessDocumentFor($command);
}
