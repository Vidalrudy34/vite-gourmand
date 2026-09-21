<?php

class ContactModel
{
    public static function create(string $titre, string $description, string $email): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO contact (titre, description, email) VALUES (:t, :d, :e)');
        $stmt->execute(['t' => $titre, 'd' => $description, 'e' => $email]);
        return (int) $pdo->lastInsertId();
    }
}
