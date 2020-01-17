<?php

namespace hiapi\endpoints;

class Endpoint
{
    public const WEB = 0x2;
    public const CLI = 0x3;
    public const QUEUE = 0x4;
    public const ALL = self::WEB | self::CLI | self::QUEUE;

    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
