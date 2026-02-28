<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Response Handler
 *
 * Encapsulates HTTP responses
 */
class Response
{
    private array $data;
    private int $statusCode;
    private array $headers;

    public function __construct(array $data, int $statusCode = 200, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Send the response
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    /**
     * Create a success response
     */
    public static function success(mixed $data, int $statusCode = 200): self
    {
        return new self([
            'success' => true,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Create an error response
     */
    public static function error(string $message, int $statusCode = 500, ?array $details = null): self
    {
        $response = [
            'success' => false,
            'error' => $message
        ];

        if ($details !== null) {
            $response['details'] = $details;
        }

        return new self($response, $statusCode);
    }

    /**
     * Create a JSON response with custom structure
     */
    public static function json(array $data, int $statusCode = 200): self
    {
        return new self($data, $statusCode);
    }
}