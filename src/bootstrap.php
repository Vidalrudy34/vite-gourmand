<?php

date_default_timezone_set('Europe/Paris');

// Chargement simple des variables d'environnement depuis .env (si présent)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

require_once __DIR__ . '/autoload.php';

$config = require __DIR__ . '/config/config.php';
if ($config['app']['env'] === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}
