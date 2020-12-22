<?php
declare(strict_types=1);

namespace hiapi\endpoints\Module\InOutControl\VO;

/**
 * Class Count
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class Count
{
    public int $count;

    public static function is(int $count): self
    {
        $self = new self;
        $self->count = $count;

        return $self;
    }
}
