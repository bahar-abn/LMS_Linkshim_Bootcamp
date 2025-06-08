<?php

namespace core;
use core\Response;
use core\Session;

// In core/Application.php (create if it doesn't exist)
namespace core;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public ?object $user = null; // Add this line
    public array $config = [];

    public function __construct(string $rootDir, array $config)
    {
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->config = $config;
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
    }

    public function run(): void
    {
        echo $this->router->resolve();
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['user']) && ($_SESSION['user_role'] ?? '') === 'admin';
    }


}
