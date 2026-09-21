<?php

/**
 * Statistiques de commandes stockées en MongoDB (base non relationnelle),
 * utilisées par l'espace administrateur : nombre de commandes par menu
 * (graphique) et chiffre d'affaires par menu avec filtres (menu, période).
 */
class StatsModel
{
    public static function enregistrer(int $commandeId, int $menuId, string $menuTitre, float $prixTotal, int $nombrePersonnes): void
    {
        $collection = MongoDatabase::getCollection('stats_commandes');
        if ($collection === null) {
            return; // Mongo indisponible : on ne bloque pas la commande
        }
        $collection->insertOne([
            'commande_id' => $commandeId,
            'menu_id' => $menuId,
            'menu_titre' => $menuTitre,
            'prix_total' => $prixTotal,
            'nombre_personnes' => $nombrePersonnes,
            'date' => new \MongoDB\BSON\UTCDateTime(),
        ]);
    }

    /** Nombre de commandes par menu (pour le graphique comparatif) */
    public static function nombreCommandesParMenu(): array
    {
        $collection = MongoDatabase::getCollection('stats_commandes');
        if ($collection === null) return [];

        $pipeline = [
            ['$group' => ['_id' => '$menu_titre', 'total' => ['$sum' => 1]]],
            ['$sort' => ['total' => -1]],
        ];
        $result = [];
        foreach ($collection->aggregate($pipeline) as $doc) {
            $result[] = ['menu' => $doc['_id'], 'total' => $doc['total']];
        }
        return $result;
    }

    /** Chiffre d'affaires par menu, avec filtres optionnels menu_id / date de début / date de fin */
    public static function chiffreAffairesParMenu(?int $menuId = null, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        $collection = MongoDatabase::getCollection('stats_commandes');
        if ($collection === null) return [];

        $match = [];
        if ($menuId) {
            $match['menu_id'] = $menuId;
        }
        if ($dateDebut || $dateFin) {
            $range = [];
            if ($dateDebut) $range['$gte'] = new \MongoDB\BSON\UTCDateTime(strtotime($dateDebut) * 1000);
            if ($dateFin) $range['$lte'] = new \MongoDB\BSON\UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
            $match['date'] = $range;
        }

        $pipeline = [];
        if (!empty($match)) {
            $pipeline[] = ['$match' => $match];
        }
        $pipeline[] = ['$group' => ['_id' => '$menu_titre', 'ca' => ['$sum' => '$prix_total'], 'nb_commandes' => ['$sum' => 1]]];
        $pipeline[] = ['$sort' => ['ca' => -1]];

        $result = [];
        foreach ($collection->aggregate($pipeline) as $doc) {
            $result[] = ['menu' => $doc['_id'], 'ca' => round($doc['ca'], 2), 'nb_commandes' => $doc['nb_commandes']];
        }
        return $result;
    }
}
