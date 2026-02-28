<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path, array|callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if (isset($this->routes[$method][$path])) {
            return $this->callHandler($this->routes[$method][$path], $request);
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $params = $this->matchPattern($pattern, $path);
            if ($params !== null) {
                $request->setRouteParams($params);
                return $this->callHandler($handler, $request);
            }
        }

        return Response::error('Not found', 404);
    }

    private function matchPattern(string $pattern, string $path): ?array
    {
        $regex = '#^' . preg_replace('/:[a-zA-Z0-9_]+/', '([^/]+)', $pattern) . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        preg_match_all('/:([a-zA-Z0-9_]+)/', $pattern, $paramNames);

        return array_combine($paramNames[1], array_slice($matches, 1)) ?: [];
    }

    private function callHandler(array|callable $handler, Request $request): Response
    {
        if (is_callable($handler)) {
            return $handler($request);
        }

        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();

        return $controller->{$method . 'Action'}($request);
    }
}
