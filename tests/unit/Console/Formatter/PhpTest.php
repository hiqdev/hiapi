<?php

namespace hiapi\tests\unit\Console\Formatter;

use Error;
use hiapi\Core\Console\Formatter\Php;
use RuntimeException;

class PhpTest extends \PHPUnit\Framework\TestCase
{
    public function testArray()
    {
        $this->assertSame(var_export(['id' => 1], true), (new Php())->formatContent(['id' => 1]));
    }

    public function testExceptionGivesMessageWithoutThrowing()
    {
        $this->assertSame("'Not existing command: x'", (new Php())->formatContent(new RuntimeException('Not existing command: x')));
    }

    public function testErrorGivesMessage()
    {
        $this->assertSame("'boom'", (new Php())->formatContent(new Error('boom')));
    }
}
