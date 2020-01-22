<?php


namespace hiapi\endpoints\Module\InOutControl;

use hiapi\endpoints\EndpointConfiguration;
use hiapi\endpoints\EndpointConfigurationInterface;
use hiapi\endpoints\Exception\EndpointBuildingException;

/**
 * Trait InOutControlBuilderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait InOutControlBuilderTrait
{
    /**
     * @psalm-var class-string
     */
    protected $take;

    /**
     * @psalm-var class-string
     */
    protected $return;

    public function take(string $className)
    {
        $this->take = $className;

        return $this;
    }

    public function return(string $className)
    {
        $this->return = $className;

        return $this;
    }

    /**
     * @param EndpointConfigurationInterface $config
     * @return $this
     * @throws EndpointBuildingException
     */
    protected function buildInOutParameters(EndpointConfigurationInterface $config)
    {
        if (empty($this->take) || empty($this->return)) {
            // TODO: think how to include command name in the exception text
            throw EndpointBuildingException::fromBuilder('Both input and output MUST be specified', $this);
        }
        $config->set('take', $this->take);
        $config->set('return', $this->return);

        return $this;
    }
}
