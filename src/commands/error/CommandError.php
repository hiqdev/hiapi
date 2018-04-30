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
    /**
     * @var BaseCommand
     */
    private $command;
    /**
     * @var \Exception
     */
    private $exception;

    public function __construct(BaseCommand $command, \Exception $exception)
    {
        $this->command = $command;
        $this->exception = $exception;
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
