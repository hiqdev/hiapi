<?php
declare(strict_types=1);

namespace hiapi\Core\Endpoint;

use Attribute;

#[Attribute]
class IsBulk
{
    public function __construct(public bool $value) {}
}
