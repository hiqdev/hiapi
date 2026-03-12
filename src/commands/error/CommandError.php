<?php

namespace hiapi\commands\error;

use hiapi\commands\BaseCommand;

/**
 * Class CommandError
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
abstract class CommandError
{
    public function __construct(private readonly BaseCommand $command, private readonly \Exception $exception)
    {
    }

    /**
     * @return BaseCommand
     */
    public function getCommand(): BaseCommand
    {
        return $this->command;
    }

    /**
     * @return \Exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }

    /**
     * @return int the status code for HTTP response
     */
    abstract public function getStatusCode(): int;
}
