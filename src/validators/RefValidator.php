<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class RefValidator
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RefValidator extends RegularExpressionValidator
{
    public $pattern = '/^[0-9A-Za-z_-]+$/';
}
