<?php

namespace hiapi\endpoints\Module\Description;

use hiapi\endpoints\EndpointConfigurationInterface;

/**
 * Trait DescriptionBuilderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait DescriptionBuilderTrait
{
    protected ?string $description = null;

    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    protected function buildDescription(EndpointConfigurationInterface $configuration): void
    {
        $configuration->set('description', $this->description);
    }
}
