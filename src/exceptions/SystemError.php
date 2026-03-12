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
    /**
     * @param mixed $data
     */
    public function __construct(private $data, ?Throwable $previous = null, int $code = 0)
    {
        parent::__construct('System error', $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}
