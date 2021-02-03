<?php

namespace hiapi\tests\unit\Utils;

use hiapi\Core\Utils\CIDR;

class CIDRTest extends \PHPUnit\Framework\TestCase
{
    protected $builder;

    public function testMatch()
    {
        $this->assertTrue(CIDR::match('1.1.1.1',        '1.1.1.1/32'));
        $this->assertTrue(CIDR::match('1.1.1.1',        '1.1.1.0/24'));
        $this->assertTrue(CIDR::match('1.1.1.255',      '1.1.1.0/24'));

        $this->assertFalse(CIDR::match('1.1.1.1',       '1.1.1.0/32'));
        $this->assertFalse(CIDR::match('1.1.1.1',       '2.2.2.0/24'));
    }

    public function testMatchBulk()
    {
        $this->assertTrue(CIDR::matchBulk('1.1.1.1',      ['0.0.0.0/0' => 1]));
        $this->assertTrue(CIDR::matchBulk('1.1.1.1',      ['1.1.1.1/32' => 1]));
        $this->assertTrue(CIDR::matchBulk('1.1.1.1',      ['2.2.2.2' => 1, '1.1.1.0/24' => 1]));
        $this->assertTrue(CIDR::matchBulk('1.1.1.255',    ['1.1.1.0/24' => 1, '2.2.2.2' => 1]));
        $this->assertTrue(CIDR::matchBulk('172.16.40.2',  ['172.16.0.0/12' => 1]));


        $this->assertFalse(CIDR::matchBulk('1.1.1.1',   []));
        $this->assertFalse(CIDR::matchBulk('1.1.1.1',   ['2.2.2.2/32' => 1, '1.1.1.0/32' => 1]));
        $this->assertFalse(CIDR::matchBulk('1.1.1.1',   ['3.3.3.3' =>1, '2.2.2.0/24' => 1]));
    }
}
