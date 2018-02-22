<?php

namespace hiapi\commands;

use Zend\Hydrator\ExtractionInterface;

/**
 * Class RuntimeErrorExtractor
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RuntimeErrorExtractor implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  object|RuntimeError $object
     * @return array
     */
    public function extract($object)
    {
        $result = $object->getCommand()->getAttributes();

        if (YII_ENV_DEV) {
            $result['_error'] = $object->getException()->getMessage();
        } else {
            $result['_error'] = 'System error';
        }

        return $result;
    }
}
