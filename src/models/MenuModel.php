<?php

class MenuModel
{
    public static function findAllWithFilters(array $filters): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle
                FROM menu m
                JOIN theme t ON t.theme_id = m.theme_id
                JOIN regime r ON r.regime_id = m.regime_id
                WHERE m.actif = 1';
        $params = [];

        if (!empty($filters['prix_max'])) {
            $sql .= ' AND m.prix_par_personne <= :prix_max';
            $params['prix_max'] = $filters['prix_max'];
        }
        if (!empty($filters['prix_min'])) {
            $sql .= ' AND m.prix_par_personne >= :prix_min';
            $params['prix_min'] = $filters['prix_min'];
        }
        if (!empty($filters['theme_id'])) {
            $sql .= ' AND m.theme_id = :theme_id';
            $params['theme_id'] = $filters['theme_id'];
        }
        if (!empty($filters['regime_id'])) {
            $sql .= ' AND m.regime_id = :regime_id';
            $params['regime_id'] = $filters['regime_id'];
        }
        if (!empty($filters['personnes_min'])) {
            $sql .= ' AND m.nombre_personnes_min >= :personnes_min';
            $params['personnes_min'] = $filters['personnes_min'];
        }
        $sql .= ' ORDER BY m.date_creation DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle
             FROM menu m
             JOIN theme t ON t.theme_id = m.theme_id
             JOIN regime r ON r.regime_id = m.regime_id
             WHERE m.menu_id = :id'
        );
        $stmt->execute(['id' => $id]);
        $menu = $stmt->fetch();
        if (!$menu) return null;

        $menu['images'] = self::getImages($id);
        $menu['plats'] = self::getPlats($id);
        return $menu;
    }

    public static function getImages(int $menuId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT url FROM menu_image WHERE menu_id = :id');
        $stmt->execute(['id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getPlats(int $menuId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.*, GROUP_CONCAT(a.libelle SEPARATOR ", ") AS allergenes
             FROM plat p
             JOIN menu_plat mp ON mp.plat_id = p.plat_id
             LEFT JOIN plat_allergene pa ON pa.plat_id = p.plat_id
             LEFT JOIN allergene a ON a.allergene_id = pa.allergene_id
             WHERE mp.menu_id = :id
             GROUP BY p.plat_id
             ORDER BY FIELD(p.type, "entree", "plat", "dessert")'
        );
        $stmt->execute(['id' => $menuId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO menu (titre, description, theme_id, regime_id, nombre_personnes_min, prix_par_personne, conditions, quantite_disponible, photo_principale)
             VALUES (:titre, :description, :theme_id, :regime_id, :min, :prix, :conditions, :qte, :photo)'
        );
        $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'theme_id' => $data['theme_id'],
            'regime_id' => $data['regime_id'],
            'min' => $data['nombre_personnes_min'],
            'prix' => $data['prix_par_personne'],
            'conditions' => $data['conditions'] ?? null,
            'qte' => $data['quantite_disponible'],
            'photo' => $data['photo_principale'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE menu SET titre=:titre, description=:description, theme_id=:theme_id, regime_id=:regime_id,
             nombre_personnes_min=:min, prix_par_personne=:prix, conditions=:conditions, quantite_disponible=:qte
             WHERE menu_id=:id'
        );
        return $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'theme_id' => $data['theme_id'],
            'regime_id' => $data['regime_id'],
            'min' => $data['nombre_personnes_min'],
            'prix' => $data['prix_par_personne'],
            'conditions' => $data['conditions'] ?? null,
            'qte' => $data['quantite_disponible'],
            'id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE menu SET actif = 0 WHERE menu_id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function decrementerStock(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE menu SET quantite_disponible = quantite_disponible - 1 WHERE menu_id = :id AND quantite_disponible > 0');
        return $stmt->execute(['id' => $id]);
    }

    public static function allThemes(): array
    {
        return Database::getConnection()->query('SELECT * FROM theme ORDER BY libelle')->fetchAll();
    }

    public static function allRegimes(): array
    {
        return Database::getConnection()->query('SELECT * FROM regime ORDER BY libelle')->fetchAll();
    }

    public static function allPlats(): array
    {
        return Database::getConnection()->query('SELECT * FROM plat ORDER BY type, nom')->fetchAll();
    }

    public static function attachPlats(int $menuId, array $platIds): void
    {
        $pdo = Database::getConnection();
        $pdo->prepare('DELETE FROM menu_plat WHERE menu_id = :id')->execute(['id' => $menuId]);
        $stmt = $pdo->prepare('INSERT INTO menu_plat (menu_id, plat_id) VALUES (:menu_id, :plat_id)');
        foreach ($platIds as $platId) {
            $stmt->execute(['menu_id' => $menuId, 'plat_id' => (int) $platId]);
        }
    }
}
