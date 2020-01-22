<?php


namespace hiapi\endpoints\Module\InOutControl;

use hiapi\endpoints\EndpointConfiguration;
use hiapi\endpoints\EndpointConfigurationInterface;
use hiapi\endpoints\Exception\EndpointBuildingException;

/**
 * Trait ExamplesAwareBuilderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait ExamplesAwareBuilderTrait
{
    /**
     * @var array
     */
    protected $examples;

    public function withExamples(array $examples)
    {
        $this->examples = $examples;

        return $this;
    }

    /**
     * @param EndpointConfigurationInterface $config
     * @return $this
     * @throws EndpointBuildingException
     */
    protected function buildExamples(EndpointConfigurationInterface $config)
    {
        $config->set('examples', $this->examples);

        return $this;
    }
}
