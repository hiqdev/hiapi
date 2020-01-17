<?php


namespace hiapi\endpoints\Module\InOutControl;

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
}
