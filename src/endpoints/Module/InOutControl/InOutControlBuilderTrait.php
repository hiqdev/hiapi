<?php


namespace hiapi\endpoints\Module\InOutControl;

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
}
