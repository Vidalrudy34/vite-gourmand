<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('utilisateur');
$user = Auth::user();
$commandes = CommandeModel::findForUser($user['id']);

$pageTitle = 'Mon espace';
require __DIR__ . '/../partials/header.php';

$labelsStatut = [
    'en_attente' => 'En attente', 'accepte' => 'Acceptée', 'en_preparation' => 'En préparation',
    'en_cours_de_livraison' => 'En cours de livraison', 'livre' => 'Livrée',
    'en_attente_retour_materiel' => 'En attente de retour du matériel', 'terminee' => 'Terminée', 'annulee' => 'Annulée',
];
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mon espace</h1>
        <a href="/espace-utilisateur/profil.php" class="btn btn-outline-secondary">Modifier mes informations</a>
    </div>

    <h2 class="h5">Mes commandes</h2>
    <?php if (empty($commandes)): ?>
        <p class="text-muted">Vous n'avez pas encore passé de commande. <a href="/menus.php">Découvrir nos menus</a></p>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover bg-white">
            <thead><tr><th>N° commande</th><th>Menu</th><th>Date prestation</th><th>Total</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($commandes as $c): ?>
                <tr>
                    <td><?= Security::clean($c['numero_commande']) ?></td>
                    <td><?= Security::clean($c['menu_titre']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['date_prestation'])) ?></td>
                    <td><?= number_format((float) $c['prix_total'], 2) ?> €</td>
                    <td><span class="badge bg-secondary"><?= $labelsStatut[$c['statut']] ?? $c['statut'] ?></span></td>
                    <td><a href="/espace-utilisateur/commande-detail.php?id=<?= (int) $c['commande_id'] ?>" class="btn btn-sm btn-outline-primary">Détail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
