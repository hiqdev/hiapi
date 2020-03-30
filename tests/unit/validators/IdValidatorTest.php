<?php

namespace hiapi\tests\unit\validators;

use hiapi\validators\IdValidator;

class IdValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var IdValidator
     */
    protected $idValidator;

    public function setUp()
    {
        $this->idValidator = new IdValidator();
    }

    /**
     * @dataProvider validIdProvider
     */
    public function testMatch($value)
    {
        $this->idValidator->validate($value);
    }

    public function validIdProvider()
    {
        yield ['1314129'];
        yield [1314129];
    }
}
