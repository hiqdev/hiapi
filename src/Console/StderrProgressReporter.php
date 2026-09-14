<?php

declare(strict_types=1);

namespace hiapi\Core\Console;

use yii\helpers\Console;

/**
 * Writes straight to STDERR, unbuffered, so a long-running console command can be watched
 * live via `nohup ... > log 2>&1 &` / tail. The injected PSR logger cannot serve this in this
 * app: it routes only to Sentry, filtered to error/warning levels, with no info-level sink.
 */
final class StderrProgressReporter implements ProgressReporterInterface
{
    public function report(string $message): void
    {
        Console::stderr($message . PHP_EOL);
    }
}
