<?php

namespace core;
use core\Response;
use core\Session;

class Application {
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public static Application $app;
    public Database $db;
    public Session $session;
    private string $rootPath;
    private array $config;

    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->rootPath = $rootPath;
        $this->config = $config;

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

    // در کلاس Application
    public function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    public function isInstructor(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'instructor';
    }

    public function isStudent(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'student';
    }
}
