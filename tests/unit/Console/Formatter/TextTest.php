<?php

namespace hiapi\tests\unit\Console\Formatter;

use hiapi\Core\Console\Formatter\Text;
use hiapi\exceptions\SystemError;
use RuntimeException;
use stdClass;

class TextTest extends \PHPUnit\Framework\TestCase
{
    private Text $formatter;

    protected function setUp(): void
    {
        $this->formatter = new Text();
    }

    public function testString()
    {
        $this->assertSame('hello', $this->formatter->formatContent('hello'));
    }

    public function testScalarsAndNull()
    {
        $this->assertSame('42', $this->formatter->formatContent(42));
        $this->assertSame('1', $this->formatter->formatContent(true));
        $this->assertSame('', $this->formatter->formatContent(null));
    }

    public function testArray()
    {
        $this->assertSame(
            "id: 1\nnames: a,b\nnested: 2,array\nobject: stdClass\n",
            $this->formatter->formatContent([
                'id' => 1,
                'names' => ['a', 'b'],
                'nested' => [2, ['x']],
                'object' => new stdClass(),
            ]),
        );
    }

    public function testThrowableGivesMessageOnly()
    {
        $e = new RuntimeException('Not existing command: .git/config', 0, new RuntimeException('inner'));

        $this->assertSame('Not existing command: .git/config', $this->formatter->formatContent($e));
    }

    public function testSystemErrorHidesPrevious()
    {
        $e = new SystemError('at ExceptionMiddleware', new RuntimeException('secret detail'));

        $this->assertSame('System error', $this->formatter->formatContent($e));
    }

    public function testObjectGivesTypeName()
    {
        $this->assertSame('stdClass', $this->formatter->formatContent(new stdClass()));
    }
}
