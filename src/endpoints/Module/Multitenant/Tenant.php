<?php

namespace hiapi\endpoints\Module\Multitenant;

interface Tenant
{
    public const WEB = 1^2;
    public const CLI = 1^3;
    public const QUEUE = 1^4;
    public const ALL = self::WEB | self::CLI | self::QUEUE;
}
