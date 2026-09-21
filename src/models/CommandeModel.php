<?php

class CommandeModel
{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $numero = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

        $stmt = $pdo->prepare(
            'INSERT INTO commande
             (numero_commande, utilisateur_id, menu_id, date_prestation, heure_livraison, adresse_livraison,
              ville_livraison, code_postal_livraison, distance_km, nombre_personnes, prix_menu, prix_livraison,
              remise, prix_total, statut)
             VALUES
             (:numero, :utilisateur_id, :menu_id, :date_prestation, :heure_livraison, :adresse,
              :ville, :cp, :distance, :nb_personnes, :prix_menu, :prix_livraison,
              :remise, :prix_total, "en_attente")'
        );
        $stmt->execute([
            'numero' => $numero,
            'utilisateur_id' => $data['utilisateur_id'],
            'menu_id' => $data['menu_id'],
            'date_prestation' => $data['date_prestation'],
            'heure_livraison' => $data['heure_livraison'],
            'adresse' => $data['adresse_livraison'],
            'ville' => $data['ville_livraison'],
            'cp' => $data['code_postal_livraison'],
            'distance' => $data['distance_km'],
            'nb_personnes' => $data['nombre_personnes'],
            'prix_menu' => $data['prix_menu'],
            'prix_livraison' => $data['prix_livraison'],
            'remise' => $data['remise'],
            'prix_total' => $data['prix_total'],
        ]);
        $id = (int) $pdo->lastInsertId();
        self::ajouterHistorique($id, 'en_attente');
        return $id;
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT c.*, m.titre AS menu_titre, m.nombre_personnes_min, u.nom, u.prenom, u.email, u.telephone
             FROM commande c
             JOIN menu m ON m.menu_id = c.menu_id
             JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
             WHERE c.commande_id = :id'
        );
        $stmt->execute(['id' => $id]);
        $commande = $stmt->fetch();
        if (!$commande) return null;
        $commande['historique'] = self::getHistorique($id);
        return $commande;
    }

    public static function findForUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT c.*, m.titre AS menu_titre
             FROM commande c JOIN menu m ON m.menu_id = c.menu_id
             WHERE c.utilisateur_id = :id ORDER BY c.date_commande DESC'
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function findAll(array $filters = []): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT c.*, m.titre AS menu_titre, u.nom, u.prenom, u.telephone, u.email
                FROM commande c
                JOIN menu m ON m.menu_id = c.menu_id
                JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
                WHERE 1=1';
        $params = [];
        if (!empty($filters['statut'])) {
            $sql .= ' AND c.statut = :statut';
            $params['statut'] = $filters['statut'];
        }
        if (!empty($filters['client'])) {
            $sql .= ' AND (u.nom LIKE :client OR u.prenom LIKE :client OR u.email LIKE :client)';
            $params['client'] = '%' . $filters['client'] . '%';
        }
        $sql .= ' ORDER BY c.date_commande DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updateStatut(int $id, string $statut, ?string $motif = null, ?string $modeContact = null): bool
    {
        $pdo = Database::getConnection();
        $sql = 'UPDATE commande SET statut = :statut';
        $params = ['statut' => $statut, 'id' => $id];

        if ($motif !== null) {
            $sql .= ', motif_annulation = :motif, mode_contact_annulation = :mode';
            $params['motif'] = $motif;
            $params['mode'] = $modeContact;
        }
        if ($statut === 'en_attente_retour_materiel') {
            $sql .= ', date_limite_restitution = DATE_ADD(NOW(), INTERVAL 10 DAY)';
        }
        if ($statut === 'terminee') {
            $sql .= ', materiel_restitue = 1';
        }
        $sql .= ' WHERE commande_id = :id';
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute($params);
        if ($ok) {
            self::ajouterHistorique($id, $statut);
        }
        return $ok;
    }

    public static function annuler(int $id, int $userId): bool
    {
        $pdo = Database::getConnection();
        // Annulation possible uniquement si la commande n'est pas encore "accepte" ou au-delà
        $stmt = $pdo->prepare("UPDATE commande SET statut = 'annulee' WHERE commande_id = :id AND utilisateur_id = :uid AND statut = 'en_attente'");
        $ok = $stmt->execute(['id' => $id, 'uid' => $userId]);
        if ($ok && $stmt->rowCount() > 0) {
            self::ajouterHistorique($id, 'annulee');
            return true;
        }
        return false;
    }

    public static function modifier(int $id, int $userId, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "UPDATE commande SET date_prestation=:date_prestation, heure_livraison=:heure, adresse_livraison=:adresse,
             ville_livraison=:ville, code_postal_livraison=:cp, distance_km=:distance, nombre_personnes=:nb,
             prix_menu=:prix_menu, prix_livraison=:prix_livraison, remise=:remise, prix_total=:total
             WHERE commande_id=:id AND utilisateur_id=:uid AND statut='en_attente'"
        );
        return $stmt->execute([
            'date_prestation' => $data['date_prestation'],
            'heure' => $data['heure_livraison'],
            'adresse' => $data['adresse_livraison'],
            'ville' => $data['ville_livraison'],
            'cp' => $data['code_postal_livraison'],
            'distance' => $data['distance_km'],
            'nb' => $data['nombre_personnes'],
            'prix_menu' => $data['prix_menu'],
            'prix_livraison' => $data['prix_livraison'],
            'remise' => $data['remise'],
            'total' => $data['prix_total'],
            'id' => $id,
            'uid' => $userId,
        ]);
    }

    public static function ajouterHistorique(int $commandeId, string $statut): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO commande_historique (commande_id, statut) VALUES (:id, :statut)');
        $stmt->execute(['id' => $commandeId, 'statut' => $statut]);
    }

    public static function getHistorique(int $commandeId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM commande_historique WHERE commande_id = :id ORDER BY date_modification ASC');
        $stmt->execute(['id' => $commandeId]);
        return $stmt->fetchAll();
    }

    public static function marquerMaterielPrete(int $id, bool $prete): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE commande SET materiel_prete = :p WHERE commande_id = :id');
        return $stmt->execute(['p' => $prete ? 1 : 0, 'id' => $id]);
    }
}
