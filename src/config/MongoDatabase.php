<?php

/**
 * Connexion à la base MongoDB (statistiques de commandes).
 * Nécessite l'extension PHP "mongodb" + le package composer "mongodb/mongodb"
 * (voir README.md). Si Mongo n'est pas disponible, l'application continue
 * de fonctionner (MySQL reste la source de vérité) mais les statistiques
 * de l'espace administrateur ne seront pas alimentées.
 */
class MongoDatabase
{
    private static ?\MongoDB\Client $client = null;
    private static bool $available = true;

    public static function getCollection(string $name)
    {
        if (!self::$available) {
            return null;
        }
        if (self::$client === null) {
            try {
                $config = require __DIR__ . '/config.php';
                self::$client = new \MongoDB\Client($config['mongo']['uri']);
                $db = self::$client->selectDatabase($config['mongo']['database']);
                return $db->selectCollection($name);
            } catch (\Throwable $e) {
                error_log('MongoDB indisponible : ' . $e->getMessage());
                self::$available = false;
                return null;
            }
        }
        $config = require __DIR__ . '/config.php';
        return self::$client->selectDatabase($config['mongo']['database'])->selectCollection($name);
    }

    public static function isAvailable(): bool
    {
        return self::$available && class_exists('\MongoDB\Client');
    }
}
