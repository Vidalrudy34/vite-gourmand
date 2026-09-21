<?php

class Security
{
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        $token = self::csrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function checkCsrf(): bool
    {
        $sent = $_POST['csrf_token'] ?? '';
        return !empty($sent) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
    }

    public static function clean(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * 10 caractères minimum, au moins 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial
     */
    public static function isPasswordStrong(string $password): bool
    {
        return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/', $password);
    }

    public static function isValidEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
