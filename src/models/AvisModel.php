<?php

class AvisModel
{
    public static function create(int $commandeId, int $userId, int $note, string $commentaire): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO avis (commande_id, utilisateur_id, note, commentaire) VALUES (:cid, :uid, :note, :com)'
        );
        return $stmt->execute(['cid' => $commandeId, 'uid' => $userId, 'note' => $note, 'com' => $commentaire]);
    }

    public static function existsForCommande(int $commandeId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM avis WHERE commande_id = :id');
        $stmt->execute(['id' => $commandeId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function findValides(int $limit = 6): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT a.*, u.prenom, u.nom FROM avis a
             JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
             WHERE a.statut = "valide" ORDER BY a.date_creation DESC LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findEnAttente(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT a.*, u.prenom, u.nom, m.titre AS menu_titre
             FROM avis a
             JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
             JOIN commande c ON c.commande_id = a.commande_id
             JOIN menu m ON m.menu_id = c.menu_id
             WHERE a.statut = "en_attente" ORDER BY a.date_creation ASC'
        );
        return $stmt->fetchAll();
    }

    public static function updateStatut(int $id, string $statut): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE avis SET statut = :s WHERE avis_id = :id');
        return $stmt->execute(['s' => $statut, 'id' => $id]);
    }
}
