<?php


namespace hiapi\endpoints\Module\InOutControl;

use hiapi\endpoints\EndpointConfiguration;
use hiapi\endpoints\EndpointConfigurationInterface;
use hiapi\endpoints\Exception\EndpointBuildingException;
use hiapi\endpoints\Module\InOutControl\VO\Collection;

/**
 * Trait InOutControlBuilderTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @template TakenType as class-string<BaseCommand>|Collection
 * @template ReturnedType as Collection|Collection<object>
 */
trait InOutControlBuilderTrait
{
    /**
     * @psalm-var TakenType
     */
    protected $take;

    /**
     * @psalm-var ReturnedType
     */
    protected $return;

    /**
     * @psalm-var boolean
     */
    protected $returnIsNullable = false;

    /**
     * @psalm-param TakenType $classNameOrObject
     * @return $this
     */
    public function take($classNameOrObject): self
    {
        $this->take = $classNameOrObject;

        return $this;
    }

    /**
     * @psalm-param TakenType $classNameOrObject
     * @return $this
     */
    public function return($classNameOrObject): self
    {
        $this->return = $classNameOrObject;

        return $this;
    }

    /**
     * @psalm-param TakenType $classNameOrObject
     * @return $this
     */
    public function returnNullable($classNameOrObject): self
    {
        $this->return($classNameOrObject);
        $this->returnIsNullable = true;

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
        $config->set('inputType', $this->take);
        $config->set('returnType', $this->return);
        $config->set('returnIsNullable', $this->returnIsNullable);

        return $this;
    }
}
