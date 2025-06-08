<?php
// In core/MainController.php
namespace core;

class MainController
{
    // In MainController.php
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
    }

}
