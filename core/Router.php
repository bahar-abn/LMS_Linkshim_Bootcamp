<?php
namespace core;

class Router {
    protected array $routes = [];
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback): void {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, $callback): void {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve() {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        $callback = $this->routes[$method][$path] ?? false;

        if (!$callback) {
            $this->response->setStatusCode(404);
            return "Not Found";
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            return call_user_func([$controller, $callback[1]], $this->request);
        }

        return call_user_func($callback, $this->request);
    }
}
