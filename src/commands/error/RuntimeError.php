<?php

namespace hiapi\commands\error;

/**
 * Class RuntimeError
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RuntimeError extends CommandError
{
    /** {@inheritdoc} */
    public function getStatusCode(): int
    {
        return 500;
    }
}
