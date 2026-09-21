<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$menu = MenuModel::find($id);
if (!$menu) {
    http_response_code(404);
    $pageTitle = 'Menu introuvable';
    require __DIR__ . '/partials/header.php';
    echo '<div class="container py-5"><p>Ce menu n\'existe pas ou n\'est plus disponible.</p><a href="/menus.php" class="btn btn-primary">Retour aux menus</a></div>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

$pageTitle = $menu['titre'];
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4">
    <nav aria-label="Fil d'ariane" class="small mb-3">
        <a href="/index.php">Accueil</a> &raquo; <a href="/menus.php">Menus</a> &raquo; <?= Security::clean($menu['titre']) ?>
    </nav>

    <div class="row g-4">
        <div class="col-md-7">
            <h1><?= Security::clean($menu['titre']) ?></h1>
            <span class="badge badge-theme"><?= Security::clean($menu['theme_libelle']) ?></span>
            <span class="badge bg-secondary"><?= Security::clean($menu['regime_libelle']) ?></span>

            <p class="mt-3"><?= nl2br(Security::clean($menu['description'])) ?></p>

            <h2 class="h5 mt-4">Composition du menu</h2>
            <ul class="list-group mb-3">
                <?php foreach ($menu['plats'] as $plat): ?>
                <li class="list-group-item">
                    <strong><?= ucfirst(Security::clean($plat['type'])) ?> :</strong> <?= Security::clean($plat['nom']) ?>
                    <?php if (!empty($plat['allergenes'])): ?>
                        <br><span class="small text-danger">Allergènes : <?= Security::clean($plat['allergenes']) ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php if (!empty($menu['images'])): ?>
            <h2 class="h5">Galerie</h2>
            <div class="row g-2 mb-3">
                <?php foreach ($menu['images'] as $img): ?>
                    <div class="col-4"><img src="<?= Security::clean($img) ?>" class="img-fluid rounded" alt="Photo du menu <?= Security::clean($menu['titre']) ?>"></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="alert alert-warning" role="alert">
                <strong>Conditions de commande :</strong>
                <?= nl2br(Security::clean($menu['conditions'] ?: 'Aucune condition particulière.')) ?>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Informations</h2>
                    <p class="mb-1"><strong><?= number_format((float) $menu['prix_par_personne'], 2) ?> € / personne</strong></p>
                    <p class="mb-1">Minimum : <?= (int) $menu['nombre_personnes_min'] ?> personnes</p>
                    <p class="mb-3">Stock disponible : <?= (int) $menu['quantite_disponible'] ?> commande(s) possible(s)</p>

                    <?php if ((int) $menu['quantite_disponible'] <= 0): ?>
                        <p class="text-danger fw-bold">Ce menu n'est plus disponible actuellement.</p>
                    <?php elseif (Auth::isLogged() && Auth::hasRole('utilisateur')): ?>
                        <a href="/commande.php?menu_id=<?= (int) $menu['menu_id'] ?>" class="btn btn-primary w-100">Commander ce menu</a>
                    <?php elseif (Auth::isLogged()): ?>
                        <p class="small text-muted">Seuls les comptes "utilisateur" peuvent passer commande.</p>
                    <?php else: ?>
                        <a href="/connexion.php?redirect=/commande.php?menu_id=<?= (int) $menu['menu_id'] ?>" class="btn btn-primary w-100">Se connecter pour commander</a>
                        <a href="/inscription.php" class="btn btn-outline-primary w-100 mt-2">Créer un compte</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
