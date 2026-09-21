<?php

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/config.php';
            $c = $config['mysql'];
            $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4";
            try {
                self::$instance = new PDO($dsn, $c['user'], $c['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log('Erreur de connexion MySQL : ' . $e->getMessage());
                http_response_code(500);
                die('Erreur de connexion à la base de données. Merci de vérifier la configuration (voir README.md).');
            }
        }
        return self::$instance;
    }
}
