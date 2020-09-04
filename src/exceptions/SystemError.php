<?php
declare(strict_types=1);

namespace hiapi\exceptions;

use Throwable;

/**
 * SystemError for unprocessable exceptions.
 * Keeps additional data.
 */
class SystemError extends \RuntimeException
{
    /** @var mixed */
    private $data;

    public function __construct($data, Throwable $previous = null, int $code = 0)
    {
        parent::__construct('System error', $code, $previous);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
