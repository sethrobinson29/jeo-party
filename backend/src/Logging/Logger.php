<?php

declare(strict_types=1);

namespace App\Logging;

use Psr\Log\LoggerInterface;

/**
 * Static facade over a PSR-3 LoggerInterface instance.
 * Call Logger::init() once at bootstrap, then use Logger::error() etc. anywhere.
 */
class Logger
{
    private static ?LoggerInterface $instance = null;

    public static function init(LoggerInterface $logger): void
    {
        self::$instance = $logger;
    }

    public static function emergency(string $message, array $context = []): void { self::$instance?->emergency($message, $context); }
    public static function alert(string $message, array $context = []): void     { self::$instance?->alert($message, $context); }
    public static function critical(string $message, array $context = []): void  { self::$instance?->critical($message, $context); }
    public static function error(string $message, array $context = []): void     { self::$instance?->error($message, $context); }
    public static function warning(string $message, array $context = []): void   { self::$instance?->warning($message, $context); }
    public static function notice(string $message, array $context = []): void    { self::$instance?->notice($message, $context); }
    public static function info(string $message, array $context = []): void      { self::$instance?->info($message, $context); }
    public static function debug(string $message, array $context = []): void     { self::$instance?->debug($message, $context); }
}
