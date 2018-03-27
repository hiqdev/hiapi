<?php

namespace hiapi\validators;

use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\validators\Validator;

/**
 * Class JsonValidator
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class JsonValidator extends Validator
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->message = '{attrubute} must be a valid JSON';
    }

    protected function validateValue($value)
    {
        try {
            Json::decode($value);
        } catch (InvalidArgumentException $e) {
            return [$this->message, []];
        }
    }
}
