<?php

namespace hiapi\tests\unit\validators;

use hiapi\validators\IdValidator;
use PHPUnit\Framework\Attributes\DataProvider;

class IdValidatorTest extends \PHPUnit\Framework\TestCase
{
    protected IdValidator $idValidator;

    public function setUp(): void
    {
        $this->idValidator = new IdValidator();
    }

    #[DataProvider('validIdProvider')]
    public function testMatch($value)
    {
        $this->idValidator->validate($value);
    }

    public static function validIdProvider(): iterable
    {
        yield ['1314129'];
        yield [1314129];
    }
}
