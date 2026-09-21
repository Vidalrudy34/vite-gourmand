<?php
require_once __DIR__ . '/../../src/bootstrap.php';

$filters = [
    'prix_min' => $_GET['prix_min'] ?? null,
    'prix_max' => $_GET['prix_max'] ?? null,
    'theme_id' => $_GET['theme_id'] ?? null,
    'regime_id' => $_GET['regime_id'] ?? null,
    'personnes_min' => $_GET['personnes_min'] ?? null,
];

$menus = MenuModel::findAllWithFilters($filters);

if (empty($menus)) {
    echo '<p class="text-muted">Aucun menu ne correspond à ces critères.</p>';
    exit;
}
?>
<div class="row g-4">
    <?php foreach ($menus as $menu): ?>
    <div class="col-md-4">
        <div class="card card-menu">
            <div class="card-body">
                <span class="badge badge-theme mb-2"><?= Security::clean($menu['theme_libelle']) ?></span>
                <span class="badge bg-secondary mb-2"><?= Security::clean($menu['regime_libelle']) ?></span>
                <h2 class="h5"><?= Security::clean($menu['titre']) ?></h2>
                <p class="small text-muted"><?= Security::clean(mb_strimwidth($menu['description'], 0, 110, '…')) ?></p>
                <p class="mb-1"><strong><?= number_format((float) $menu['prix_par_personne'], 2) ?> € / personne</strong></p>
                <p class="small text-muted">À partir de <?= (int) $menu['nombre_personnes_min'] ?> personnes</p>
                <a href="/menu-detail.php?id=<?= (int) $menu['menu_id'] ?>" class="btn btn-outline-primary btn-sm">Voir le détail</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
