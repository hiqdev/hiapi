<?php

namespace hiapi\commands;

use Zend\Hydrator\ExtractionInterface;

/**
 * Class LogicallErrorExtractor
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class LogicallErrorExtractor implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  object|RuntimeError $object
     * @return array
     */
    public function extract($object)
    {
        if (!$object instanceof RuntimeError) {
            // TODO ???
            throw new \Exception('Not implemented');
        }

        $result = $object->getCommand()->getAttributes();
        $result['_error'] = $object->getException()->getMessage();

        return $result;
    }
}
