<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class LongRefValidator
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class LongRefValidator extends RegularExpressionValidator
{
    // TODO improve pattern
    public $pattern = '/^[0-9A-Za-z_,-]+$/';
}
