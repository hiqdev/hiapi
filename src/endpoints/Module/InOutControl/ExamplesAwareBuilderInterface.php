<?php


namespace hiapi\endpoints\Module\InOutControl;

/**
 * Interface ExamplesAwareBuilderInterface
 *
 * @template T of ExamplesAwareBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface ExamplesAwareBuilderInterface
{
    /**
     * // TODO: type
     *
     * @param array $examples
     * @return T
     */
    public function withExamples(array $examples);
}
