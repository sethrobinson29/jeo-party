<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router Class
 *
 * Matches requests to controller actions
 */
class Router
{
    private array $routes = [];

    /**
     * Add a route
     *
     * @param string $method HTTP method
     * @param string $path URL path pattern
     * @param array $handler [ControllerClass, method] or callable
     */
    public function addRoute(string $method, string $path, array|callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    /**
     * Dispatch request to appropriate controller
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        // Check for exact match
        if (isset($this->routes[$method][$path])) {
            return $this->handleRoute($this->routes[$method][$path], $request);
        }

        // Check for pattern matches (e.g., /api/clues/:id)
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $params = $this->matchPattern($pattern, $path);
            if ($params !== null) {
                return $this->handleRoute($handler, $request, $params);
            }
        }

        // No route found
        return Response::error('Endpoint not found', 404, [
            'path' => $path,
            'method' => $method
        ]);
    }

    /**
     * Match a path pattern against a path
     * Returns parameters if matched, null otherwise
     */
    private function matchPattern(string $pattern, string $path): ?array
    {
        // Convert pattern like /api/clues/:id to regex
        $regex = preg_replace('/:[a-zA-Z0-9_]+/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $path, $matches)) {
            array_shift($matches); // Remove full match

            // Extract parameter names
            preg_match_all('/:([a-zA-Z0-9_]+)/', $pattern, $paramNames);
            $paramNames = $paramNames[1];

            // Combine names with values
            return array_combine($paramNames, $matches) ?: [];
        }

        return null;
    }

    /**
     * Handle a matched route
     */
    private function handleRoute(array|callable $handler, Request $request, array $params = []): Response
    {
        // Inject params into request if any
        foreach ($params as $key => $value) {
            // Store route params for controller access
            $request->setRouteParams($params);
        }

        if (is_callable($handler)) {
            return $handler($request);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;
            $method .= 'Action';

            if (!class_exists($controllerClass)) {
                return Response::error("Controller not found: $controllerClass", 500);
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                return Response::error("Method not found: $method", 500);
            }

            return $controller->$method($request);
        }

        return Response::error('Invalid route handler', 500);
    }
}