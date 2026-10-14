<?php

declare(strict_types=1);

/**
 * The smallest router that still feels like a router:
 * map "GET /path" to a [Controller::class, 'method'] pair (or a closure).
 */
final class Router
{
    /** @var array<string, callable|array{0: class-string, 1: string}> */
    private array $routes = [];

    public function get(string $path, array|callable $handler): void
    {
        $this->routes['GET ' . $path] = $handler;
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->routes['POST ' . $path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        // Strip the query string and any trailing slash ("/about/" -> "/about").
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $handler = $this->routes[$method . ' ' . $path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 — Not Found';
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->{$action}();
            return;
        }

        $handler();
    }
}
