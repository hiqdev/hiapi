<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class UsernameValidator validates username
 * TODO: allow emails also?
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class UsernameValidator extends RegularExpressionValidator
{
    public $pattern = '/^[a-z][a-z0-9_]{1,31}$/';

    public function init()
    {
        parent::init();

        $this->message = 'Value is not a valid username';
    }
}
