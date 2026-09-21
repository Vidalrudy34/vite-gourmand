<?php
/**
 * Configuration de l'application - lit les variables d'environnement
 * si présentes (déploiement), sinon utilise des valeurs par défaut
 * adaptées à une installation locale (voir README.md).
 */


if (!function_exists('env')) {
function env(string $key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}
}

return [
    'app' => [
        'name' => 'Vite & Gourmand',
        'url' => env('APP_URL', 'http://localhost:8000'),
        'env' => env('APP_ENV', 'local'),
    ],
    'mysql' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_NAME', 'vite_gourmand'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ],
    'mongo' => [
        'uri' => env('MONGO_URI', 'mongodb://127.0.0.1:27017'),
        'database' => env('MONGO_DB', 'vite_gourmand_stats'),
    ],
    'mail' => [
        'from' => env('MAIL_FROM', 'contact@vitegourmand.fr'),
        'from_name' => 'Vite & Gourmand',
        // en local, les mails sont écrits dans storage/mails/ au lieu d'être envoyés
        'log_only' => (bool) env('MAIL_LOG_ONLY', true),
    ],
    'entreprise' => [
        'ville_reference' => 'Bordeaux',
        'frais_livraison_base' => 5.00,
        'frais_par_km' => 0.59,
        'seuil_reduction_personnes' => 5, // + 5 pers. que le minimum du menu => -10%
        'taux_reduction' => 0.10,
        'jours_max_restitution_materiel' => 10,
        'frais_materiel_non_restitue' => 600.00,
    ],
];
