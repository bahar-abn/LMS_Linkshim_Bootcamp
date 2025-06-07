<?php
// In core/MainController.php
namespace core;

class MainController
{
    // In MainController.php
    protected function render($view, $params = [])
    {
        extract($params, EXTR_SKIP);

        $viewFile = Application::$ROOT_DIR . "/views/$view.php";
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: $viewFile");
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Optional: wrap with layout
        include Application::$ROOT_DIR . "/views/layouts/main.php";
    }

}
