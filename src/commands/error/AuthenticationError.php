<?php

namespace hiapi\commands\error;

/**
 * Class AuthenticationError
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class AuthenticationError extends CommandError
{
    /** {@inheritdoc} */
    public function getStatusCode(): int
    {
        return 401;
    }
}
