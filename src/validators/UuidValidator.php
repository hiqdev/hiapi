<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class UuidValidator
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class UuidValidator extends RegularExpressionValidator
{
    public $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->message = '{attribute} must be a valid UUID';
    }
}
