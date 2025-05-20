<?php
return [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
    'userClass' => \app\models\User::class,
    'baseUrl' => $_ENV['BASE_URL'] ?? 'http://localhost:8000',
    'mailer' => [
        'host' => $_ENV['MAILER_HOST'] ?? 'smtp.example.com',
        'port' => $_ENV['MAILER_PORT'] ?? 587,
        'username' => $_ENV['MAILER_USERNAME'] ?? '',
        'password' => $_ENV['MAILER_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAILER_ENCRYPTION'] ?? 'tls',
    ]
];