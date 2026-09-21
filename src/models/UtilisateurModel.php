<?php

class UtilisateurModel
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT u.*, r.libelle AS role_libelle
             FROM utilisateur u JOIN role r ON r.role_id = u.role_id
             WHERE u.email = :email'
        );
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT u.*, r.libelle AS role_libelle
             FROM utilisateur u JOIN role r ON r.role_id = u.role_id
             WHERE u.utilisateur_id = :id'
        );
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $data, string $roleLibelle = 'utilisateur'): int
    {
        $pdo = Database::getConnection();
        $roleStmt = $pdo->prepare('SELECT role_id FROM role WHERE libelle = :libelle');
        $roleStmt->execute(['libelle' => $roleLibelle]);
        $roleId = $roleStmt->fetchColumn();

        $stmt = $pdo->prepare(
            'INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, telephone, adresse_postale, ville, code_postal, role_id)
             VALUES (:email, :mdp, :nom, :prenom, :telephone, :adresse, :ville, :cp, :role_id)'
        );
        $stmt->execute([
            'email' => $data['email'],
            'mdp' => password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse_postale'],
            'ville' => $data['ville'],
            'cp' => $data['code_postal'],
            'role_id' => $roleId,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function updateProfil(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE utilisateur SET nom = :nom, prenom = :prenom, telephone = :telephone,
             adresse_postale = :adresse, ville = :ville, code_postal = :cp WHERE utilisateur_id = :id'
        );
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse_postale'],
            'ville' => $data['ville'],
            'cp' => $data['code_postal'],
            'id' => $id,
        ]);
    }

    public static function setResetToken(string $email, string $token, string $expire): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE utilisateur SET token_reset = :t, token_reset_expire = :e WHERE email = :email');
        return $stmt->execute(['t' => $token, 'e' => $expire, 'email' => $email]);
    }

    public static function findByResetToken(string $token): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE token_reset = :t AND token_reset_expire > NOW()');
        $stmt->execute(['t' => $token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function updatePassword(int $id, string $newPassword): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE utilisateur SET mot_de_passe = :mdp, token_reset = NULL, token_reset_expire = NULL WHERE utilisateur_id = :id');
        return $stmt->execute(['mdp' => password_hash($newPassword, PASSWORD_BCRYPT), 'id' => $id]);
    }

    /** Création d'un compte employé par l'administrateur */
    public static function createEmploye(string $email, string $motDePasseTemporaire, string $nom, string $prenom): int
    {
        return self::create([
            'email' => $email,
            'mot_de_passe' => $motDePasseTemporaire,
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => '',
            'adresse_postale' => '',
            'ville' => '',
            'code_postal' => '',
        ], 'employe');
    }

    public static function toggleActif(int $id, bool $actif): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE utilisateur SET actif = :actif WHERE utilisateur_id = :id');
        return $stmt->execute(['actif' => $actif ? 1 : 0, 'id' => $id]);
    }

    public static function listEmployes(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            "SELECT u.* FROM utilisateur u JOIN role r ON r.role_id = u.role_id WHERE r.libelle = 'employe' ORDER BY u.nom"
        );
        return $stmt->fetchAll();
    }
}
