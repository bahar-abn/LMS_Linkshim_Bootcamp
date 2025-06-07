<?php
namespace core;

class Request {
    private array $routeParams = [];

    public function getPath(): string {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'];

        $basePath = str_replace('/index.php', '', $scriptName);

        $path = str_replace($basePath, '', $requestUri);

        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        return rtrim($path, '/') ?: '/';
    }

    public function getMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody(): array {
        $body = [];

        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    // Route Params

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function getRouteParam(string $key): ?string
    {
        return $this->routeParams[$key] ?? null;
    }
}
