<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        return $path === '' ? '/' : '/' . $path;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath($uri);
        $method = strtoupper($method);

        // 1. Direct exact match check
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            $controller = new $handler[0]();
            $action = $handler[1];
            $controller->$action();
            return;
        }

        // 2. Dynamic parameterized route matching e.g. /admin/enquiries/{id}
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $handler) {
                if (strpos($routePath, '{') === false) {
                    continue;
                }

                $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches); // Remove full match
                    $controller = new $handler[0]();
                    $action = $handler[1];
                    call_user_func_array([$controller, $action], $matches);
                    return;
                }
            }
        }

        // 404 Fallback
        http_response_code(404);
        View::render('404', ['title' => 'Page Not Found - FEEBLE EXPORTS']);
    }
}
