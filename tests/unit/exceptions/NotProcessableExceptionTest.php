<?php

declare(strict_types=1);

namespace hiapi\tests\unit\exceptions;

use hiapi\exceptions\NotProcessableException;
use PHPUnit\Framework\TestCase;

class NotProcessableExceptionTest extends TestCase
{
    public function testKeepsFractionalProgressionMultiplier(): void
    {
        $exception = (new NotProcessableException('failed'))->retryProgressively(5, 1.5);

        $this->assertSame(1.5, $exception->getProgressionMultiplier());
    }

    public function testDefaultsToConstantDelay(): void
    {
        $this->assertSame(1.0, (new NotProcessableException('failed'))->getProgressionMultiplier());
    }
}
