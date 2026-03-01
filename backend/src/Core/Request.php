<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $routeParams = [];

    private function __construct(
        private string $method,
        private string $uri,
        private string $path,
        private array $query = [],
        private array $body = [],
        private array $headers = [],
    ) {}

    public static function createFromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $body = [];
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            $body = str_contains($contentType, 'application/json')
                ? json_decode(file_get_contents('php://input'), true) ?? []
                : $_POST;
        }

        return new self(
            $method,
            $uri,
            parse_url($uri, PHP_URL_PATH) ?? '/',
            $_GET,
            $body,
            getallheaders() ?: [],
        );
    }

    public function isValid(): bool
    {
        $dangerousPatterns = ['/\.\./i', '/<script/i', '/union.*select/i', '/eval\(/i', '/base64_decode/i'];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $this->uri)) {
                return false;
            }
        }

        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'];

        return in_array($this->method, $allowedMethods) && strlen($this->uri) <= 2048;
    }

    public function getMethod(): string { return $this->method; }
    public function getUri(): string { return $this->uri; }
    public function getPath(): string { return $this->path; }
    public function getRouteParams(): array { return $this->routeParams; }
    public function setRouteParams(array $params): void { $this->routeParams = $params; }

    public function getQuery(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->query : ($this->query[$key] ?? $default);
    }

    public function getBody(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->body : ($this->body[$key] ?? $default);
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
}
