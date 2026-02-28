<?php

declare(strict_types=1);

namespace App\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FileLogger implements LoggerInterface
{
    public function __construct(private readonly string $path)
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function log(mixed $level, string|\Stringable $message, array $context = []): void
    {
        $context  = $this->normalizeContext($context);
        $message  = $this->interpolate((string) $message, $context);
        $timestamp = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        $contextStr = empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $entry = sprintf('[%s] %s: %s%s', $timestamp, strtoupper((string) $level), $message, $contextStr);

        file_put_contents($this->path, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function normalizeContext(array $context): array
    {
        return array_map(static function (mixed $value): mixed {
            if ($value instanceof \Throwable) {
                return sprintf('%s in %s:%d', $value->getMessage(), $value->getFile(), $value->getLine());
            }
            return $value;
        }, $context);
    }

    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $replace['{' . $key . '}'] = (string) $value;
            }
        }
        return strtr($message, $replace);
    }

    public function emergency(string|\Stringable $message, array $context = []): void { $this->log(LogLevel::EMERGENCY, $message, $context); }
    public function alert(string|\Stringable $message, array $context = []): void     { $this->log(LogLevel::ALERT,     $message, $context); }
    public function critical(string|\Stringable $message, array $context = []): void  { $this->log(LogLevel::CRITICAL,  $message, $context); }
    public function error(string|\Stringable $message, array $context = []): void     { $this->log(LogLevel::ERROR,     $message, $context); }
    public function warning(string|\Stringable $message, array $context = []): void   { $this->log(LogLevel::WARNING,   $message, $context); }
    public function notice(string|\Stringable $message, array $context = []): void    { $this->log(LogLevel::NOTICE,    $message, $context); }
    public function info(string|\Stringable $message, array $context = []): void      { $this->log(LogLevel::INFO,      $message, $context); }
    public function debug(string|\Stringable $message, array $context = []): void     { $this->log(LogLevel::DEBUG,     $message, $context); }
}
