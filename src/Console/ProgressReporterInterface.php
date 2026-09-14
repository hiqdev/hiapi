<?php

declare(strict_types=1);

namespace hiapi\Core\Console;

interface ProgressReporterInterface
{
    public function report(string $message): void;
}
