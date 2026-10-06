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

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            $controller = new $handler[0]();
            $action = $handler[1];
            $controller->$action();
            return;
        }

        // 404 Fallback
        http_response_code(404);
        View::render('404', ['title' => 'Page Not Found - FEEBLE EXPORTS']);
    }
}
