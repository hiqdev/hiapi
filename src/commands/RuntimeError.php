<?php

namespace hiapi\commands;

/**
 * Class RuntimeError
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RuntimeError
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
}
