<?php
// In core/MainController.php
namespace core;

class MainController
{
    // In MainController.php


    protected function redirect($url)
    {
        Application::$app->response->redirect($url);
        exit;
    }
    public function render3(string $view, array $params = []): void
    {
        extract($params);
        require_once Application::$ROOT_DIR . "/views/$view.php";
    }
    public function render2($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function render($view, $params = [], $useLayout = true)
    {
        extract($params);

        ob_start();
        include __DIR__ . "/../views/$view.php";
        $content = ob_get_clean();

        if ($useLayout) {
            include __DIR__ . '/../views/layout/main.php';
        } else {
            echo $content;
        }


    }}
