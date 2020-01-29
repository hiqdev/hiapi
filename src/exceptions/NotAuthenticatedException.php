<?php


namespace hiapi\exceptions;

use hiapi\exceptions\domain\DomainException;

/**
 * Class NotAuthenticatedException
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class NotAuthenticatedException extends DomainException
{
    /**
     * NotAuthenticatedException constructor.
     */
    public function __construct(string $message = null)
    {
        parent::__construct($message ?: "Not authenticated");
    }
}
