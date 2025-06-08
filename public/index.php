<?php
// At the very top of index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Disable output buffering
while (ob_get_level()) {
    ob_end_clean();
}
use core\Application;

// Load Composer autoloader if needed
require_once __DIR__ . '/../vendor/autoload.php';

// Load the config
$config = require __DIR__ . '/../config/config.php';

$app = new Application(dirname(__DIR__), $config);

// If running in CLI (e.g., for migrations)
if (php_sapi_name() === 'cli') {
    $command = $argv[1] ?? null;

    if ($command === 'migrate') {
        $app->db->applyMigrations();
        exit;
    }

    echo "Unknown command.\n";
    exit;
}

// 👇 Only include routes if not CLI
require_once __DIR__ . '/../routes.php';

$app->run();
