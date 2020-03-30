<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class IdValidator validates an integer ID
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class IdValidator extends RegularExpressionValidator
{
    public $pattern = '/^\d+$/';

    public function init()
    {
        parent::init();

        $this->message = 'Value is not a valid numeric integer';
    }
}
