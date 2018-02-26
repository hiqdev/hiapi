<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class CountryCodeValidator validates country code in ISO 3166-1 alpha-2 format
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CountryCodeValidator extends RegularExpressionValidator
{
    public $pattern = '/^[A-Z]{2}$/';

    public $message = '{attribute} is not a valid ISO 3166-1 alpha-2 country code';
}
