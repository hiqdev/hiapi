<?php

namespace hiapi\exceptions\domain;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class RequiredInputException extends DomainException
{
    public function __construct(private readonly string $field, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("required input `{$this->field}`", $code, $previous);
    }

    public function getField()
    {
        return $this->field;
    }
}
