<?php

namespace core;

use core\Response;
use core\Session;
use core\Request;
use core\Router;
use core\Database;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;

    public Request $request;
    public Response $response;
    public Router $router;
    public Session $session;
    public Database $db;

    public ?object $user = null;
    public array $config = [];

    public function __construct(string $rootDir, array $config)
    {
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;

        $this->config = $config;

        // ✅ Initialize in correct order
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
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
