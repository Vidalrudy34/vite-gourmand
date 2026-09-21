<?php

class HoraireModel
{
    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM horaire ORDER BY FIELD(jour,'lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche')");
        return $stmt->fetchAll();
    }

    public static function update(int $id, ?string $ouverture, ?string $fermeture, bool $ferme): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE horaire SET heure_ouverture = :o, heure_fermeture = :f, ferme = :ferme WHERE horaire_id = :id');
        return $stmt->execute([
            'o' => $ferme ? null : $ouverture,
            'f' => $ferme ? null : $fermeture,
            'ferme' => $ferme ? 1 : 0,
            'id' => $id,
        ]);
    }
}
