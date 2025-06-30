<?php
// Remove any whitespace before <?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lms-php-mvc/public');
}

return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=lms_db',
        'user' => 'root',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ]
];