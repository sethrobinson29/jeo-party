<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    private function __construct(
        private array $data,
        private int $statusCode = 200,
        private array $headers = [],
    ) {}

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    public static function success(mixed $data, int $statusCode = 200): self
    {
        return new self(['success' => true, 'data' => $data], $statusCode);
    }

    public static function error(string $message, int $statusCode = 500, ?array $details = null): self
    {
        $body = ['success' => false, 'error' => $message];

        if ($details !== null) {
            $body['details'] = $details;
        }

        return new self($body, $statusCode);
    }

    public static function json(array $data, int $statusCode = 200): self
    {
        return new self($data, $statusCode);
    }
}
