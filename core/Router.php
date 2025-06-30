<?php
namespace core;

use controllers\CourseController;

class Router
{
    protected array $routes = [];
    public Request $request;
    public Response $response;
    private array $routeParams = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback): void
    {
        $this->routes['get'][$this->normalize($path)] = $callback;
    }

    public function post(string $path, $callback): void
    {
        $this->routes['post'][$this->normalize($path)] = $callback;
    }

    public function resolve()
    {
        $method = $this->request->getMethod();
        $path = $this->normalize($this->request->getPath());

        $callback = $this->matchRoute($method, $path);

        if (!$callback) {
            $this->response->setStatusCode(404);
            return "404 | Not Found";
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $methodName = $callback[1];

            // Inject route params into Request object
            $this->request->setRouteParams($this->routeParams);

            // Get method parameters using reflection
            $reflection = new \ReflectionMethod($controller, $methodName);
            $parameters = $reflection->getParameters();
            $args = [];

            foreach ($parameters as $param) {
                $paramName = $param->getName();
                $paramType = $param->getType();

                // If parameter is Request object
                if ($paramType && $paramType->getName() === Request::class) {
                    $args[] = $this->request;
                }
                // If parameter is a route parameter
                elseif (array_key_exists($paramName, $this->routeParams)) {
                    $args[] = $this->routeParams[$paramName];
                }
                // Otherwise try to get from request body
                else {
                    $args[] = $this->request->getBody()[$paramName] ?? null;
                }
            }

            return call_user_func_array([$controller, $methodName], $args);
        }

        return call_user_func($callback, $this->request);
    }

    private function matchRoute(string $method, string $path)
    {
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route => $callback) {
            $routePattern = preg_replace('/\{([\w]+)\}/', '([^\/]+)', $route);
            $routePattern = '#^' . $routePattern . '$#';

            if (preg_match($routePattern, $path, $matches)) {
                array_shift($matches);

                // Extract param names
                preg_match_all('/\{([\w]+)\}/', $route, $paramNames);
                $this->routeParams = array_combine($paramNames[1], $matches);

                return $callback;
            }
        }

        return false;
    }

    private function normalize(string $path): string
    {
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
    public function renderView($view, $params = [])
    {
        // Extract variables to be available in the view
        extract($params, EXTR_SKIP);

        // Start output buffering
        ob_start();

        // Include the view file
        $viewFile = Application::$ROOT_DIR . "/views/$view.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View file $viewFile not found");
        }

        // Return the rendered content
        return ob_get_clean();
    }
}