<?php

class Price
{
    /**
     * Frais de livraison : gratuit à Bordeaux, sinon 5€ + 0,59€/km parcouru.
     */
    public static function fraisLivraison(string $ville, float $distanceKm): float
    {
        $config = require __DIR__ . '/../config/config.php';
        $e = $config['entreprise'];
        if (mb_strtolower(trim($ville)) === mb_strtolower($e['ville_reference'])) {
            return 0.0;
        }
        return round($e['frais_livraison_base'] + $e['frais_par_km'] * max(0, $distanceKm), 2);
    }

    /**
     * Réduction de 10% si le nombre de personnes est au moins 5 de plus
     * que le minimum indiqué dans le menu.
     */
    public static function remise(float $prixMenu, int $nombrePersonnes, int $minimumMenu): float
    {
        $config = require __DIR__ . '/../config/config.php';
        $e = $config['entreprise'];
        if ($nombrePersonnes >= $minimumMenu + $e['seuil_reduction_personnes']) {
            return round($prixMenu * $e['taux_reduction'], 2);
        }
        return 0.0;
    }

    public static function calculerCommande(float $prixParPersonne, int $nombrePersonnes, int $minimumMenu, string $ville, float $distanceKm): array
    {
        $prixMenu = round($prixParPersonne * $nombrePersonnes, 2);
        $remise = self::remise($prixMenu, $nombrePersonnes, $minimumMenu);
        $fraisLivraison = self::fraisLivraison($ville, $distanceKm);
        $total = round($prixMenu - $remise + $fraisLivraison, 2);
        return [
            'prix_menu' => $prixMenu,
            'remise' => $remise,
            'frais_livraison' => $fraisLivraison,
            'prix_total' => $total,
        ];
    }
}
