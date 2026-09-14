<?php

declare(strict_types=1);

namespace hiapi\Core\Console;

final class NullProgressReporter implements ProgressReporterInterface
{
    public function report(string $message): void
    {
    }
}
