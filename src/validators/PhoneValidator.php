<?php

namespace hiapi\validators;

use yii\validators\RegularExpressionValidator;

/**
 * Class PhoneValidator
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PhoneValidator extends RegularExpressionValidator
{
    public function __construct($config = [])
    {
        $this->pattern = $this->replaceParams(
            '/^\+?({part1})? ?(?(?=\()(\({part2}\) ?{part3})|([. -]?({part2}[. -]*)?{part3}))$/',
            [
                'part1' => '\d{0,3}',
                'part2' => '\d{1,3}',
                'part3' => '((\d{3,5})[. -]?(\d{4})|(\d{2}[. -]?){4})',
            ]
        );

        parent::__construct($config);

        $this->message = '{attribute} must be a valid phone number';
    }

    private function replaceParams($format, array $params)
    {
        $string = $format;
        foreach ($params as $name => $value) {
            $string = str_replace('{'.$name.'}', $value, $string);
        }

        return $string;
    }
}
