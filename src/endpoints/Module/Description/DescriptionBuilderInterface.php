<?php

namespace hiapi\endpoints\Module\Description;

/**
 * Interface DescriptionBuilderInterface
 *
 * @template T of DescriptionBuilderInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface DescriptionBuilderInterface
{
    /**
     * @param string $description
     * @return T
     */
    public function description(string $description);
}
