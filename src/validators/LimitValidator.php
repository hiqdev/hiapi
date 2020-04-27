<?php

namespace hiapi\validators;

use yii\validators\Validator;

/**
 * Validates a a value to be used as a LIMIT
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class LimitValidator extends Validator
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->message = 'The limit is not valid';
    }

    public function validateAttribute($model, $attribute)
    {
        $model->$attribute = mb_strtolower($model->$attribute);

        parent::validateAttribute($model, $attribute);
    }

    protected function validateValue($value)
    {
        if (empty($value) || $value === 'all') {
            return null;
        }

        if (is_numeric($value)) {
            return null;
        }

        return [$this->message, null];
    }
}
