<?php

namespace hiapi\jsonApi;

use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;

/**
 * Trait JsonApiObjectProviderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait JsonApiObjectProviderTrait
{
    public function getJsonApi(): ?JsonApiObject
    {
        return new JsonApiObject("1.0");
    }
}
