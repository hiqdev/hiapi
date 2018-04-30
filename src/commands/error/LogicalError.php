<?php

namespace hiapi\commands\error;

/**
 * Class LogicalError
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class LogicalError extends CommandError
{
    /** {@inheritdoc} */
    public function getStatusCode(): int
    {
        return 422;
    }
}
