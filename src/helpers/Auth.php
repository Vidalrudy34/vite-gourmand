<?php

class Auth
{
    public static function login(array $utilisateur): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $utilisateur['utilisateur_id'],
            'email' => $utilisateur['email'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'telephone' => $utilisateur['telephone'],
            'adresse_postale' => $utilisateur['adresse_postale'],
            'ville' => $utilisateur['ville'],
            'code_postal' => $utilisateur['code_postal'],
            'role' => $utilisateur['role_libelle'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function isLogged(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        if (!self::isLogged()) return false;
        return in_array($_SESSION['user']['role'], $roles, true);
    }

    public static function requireLogin(string $redirectTo = '/connexion.php'): void
    {
        if (!self::isLogged()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            die('Accès refusé : vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
    }
}
