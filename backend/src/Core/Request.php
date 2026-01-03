<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Request Handler
 *
 * Encapsulates and validates HTTP requests
 */
class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $query;
    private array $body;
    private array $headers;

    public function __construct(
        string $method,
        string $uri,
        array $query = [],
        array $body = [],
        array $headers = []
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $this->query = $query;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Create Request from PHP globals
     */
    public static function createFromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $query = $_GET;

        // Parse body based on content type
        $body = [];
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (str_contains($contentType, 'application/json')) {
                $json = file_get_contents('php://input');
                $body = json_decode($json, true) ?? [];
            } else {
                $body = $_POST;
            }
        }

        // Get headers
        $headers = getallheaders() ?: [];

        return new self($method, $uri, $query, $body, $headers);
    }

    /**
     * Validate request for security concerns
     */
    public function isValid(): bool
    {
        // Check for common attack patterns in URI
        $dangerousPatterns = [
            '/\.\./i',           // Directory traversal
            '/<script/i',        // XSS
            '/union.*select/i',  // SQL injection
            '/eval\(/i',         // Code injection
            '/base64_decode/i',  // Obfuscated payloads
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $this->uri)) {
                return false;
            }
        }

        // Validate HTTP method
        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'];
        if (!in_array($this->method, $allowedMethods)) {
            return false;
        }

        // Validate URI length
        if (strlen($this->uri) > 2048) {
            return false;
        }

        return true;
    }

    /**
     * Simple rate limiting check
     * In production, use Redis or a proper rate limiter
     */
    public function checkRateLimit(): bool
    {
        // For now, always allow
        // TODO: Implement proper rate limiting with Redis/Memcached
        return true;
    }

    // Getters

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function getBody(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->body;
        }

        return $this->body[$key] ?? $default;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}