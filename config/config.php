<?php
const BASE_URL = '/lms-php-mvc/public';

return [
    'BASE_URL' => 'http://localhost/lms-php-mvc/public',

    'db' => [
        'dsn' => 'mysql:host=localhost;port=3307;dbname=lms_db',
        'user' => 'root',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ]
];