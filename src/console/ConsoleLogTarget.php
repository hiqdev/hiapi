<?php

namespace hiapi\console;

use hidev\components\Log;
use Psr\Log\LogLevel;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class ConsoleLogTarget
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ConsoleLogTarget extends \yii\log\Target
{
    public $exportInterval = 1;

    public $exportContext = [
        LogLevel::EMERGENCY => false,
        LogLevel::ERROR     => false,
        LogLevel::ALERT     => false,
        LogLevel::CRITICAL  => false,
        LogLevel::WARNING   => false,
    ];

    public $styles = [
        LogLevel::EMERGENCY => [Console::BOLD, Console::BG_RED],
        LogLevel::ERROR     => [Console::FG_RED, Console::BOLD],
        LogLevel::ALERT     => [Console::FG_RED],
        LogLevel::CRITICAL  => [Console::FG_RED],
        LogLevel::WARNING   => [Console::FG_YELLOW],
    ];

    public function export()
    {
        foreach ($this->messages as $message) {
            $this->out($message[0], $message[1]);
            $this->outContext($message[0], $message[2]);
        }
    }

    private function outContext($level, $context)
    {
        if ($this->exportContext[$level] ?? false) {
            $export = VarDumper::export($context);
            Console::stdout($export . "\n");
        }
    }

    public function out($level, $message)
    {
        $style = $this->styles[$level];
        if ($style) {
            $message = Console::ansiFormat($message, $style);
        } else {
            return;
        }
        Console::stdout($message . "\n");
    }

    protected function getContextMessage()
    {
        return '';
    }
}
