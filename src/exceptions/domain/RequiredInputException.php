<?php

namespace hiapi\exceptions\domain;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class RequiredInputException extends DomainException
{
    private $field;

    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        $this->field = $message;
        parent::__construct("required input `$message`", $code, $previous);
    }

    public function getField()
    {
        return $this->field;
    }
}
