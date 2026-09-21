<?php
require_once __DIR__ . '/../src/bootstrap.php';

$pageTitle = 'Accueil';
$avis = AvisModel::findValides(6);
$menusPhares = MenuModel::findAllWithFilters([]);
$menusPhares = array_slice($menusPhares, 0, 3);

require __DIR__ . '/partials/header.php';
?>

<section class="hero text-center">
    <div class="container">
        <h1 class="display-5 fw-bold">Vite &amp; Gourmand</h1>
        <p class="lead">Traiteur événementiel à Bordeaux depuis 25 ans — des menus faits maison pour tous vos événements.</p>
        <a href="/menus.php" class="btn btn-primary btn-lg mt-2">Découvrir nos menus</a>
    </div>
</section>

<section class="container py-5">
    <div class="row align-items-center g-4">
        <div class="col-md-6">
            <h2>Notre entreprise</h2>
            <p>« Vite &amp; Gourmand » est une entreprise familiale fondée par Julie et José. Depuis 25 ans à Bordeaux,
            nous concevons des menus pour tous vos événements — un simple repas, Noël, Pâques ou toute occasion
            particulière. Nos menus évoluent en permanence pour vous surprendre.</p>
        </div>
        <div class="col-md-6">
            <h2>Notre équipe</h2>
            <p>Une équipe passionnée et professionnelle : de la préparation en cuisine à la livraison chez vous,
            nous mettons tout notre savoir-faire au service de votre événement, avec des produits frais et de saison.</p>
        </div>
    </div>
</section>

<?php if (!empty($menusPhares)): ?>
<section class="container py-4">
    <h2 class="mb-4">Quelques-uns de nos menus</h2>
    <div class="row g-4">
        <?php foreach ($menusPhares as $menu): ?>
        <div class="col-md-4">
            <div class="card card-menu">
                <div class="card-body">
                    <span class="badge badge-theme mb-2"><?= Security::clean($menu['theme_libelle']) ?></span>
                    <h3 class="h5"><?= Security::clean($menu['titre']) ?></h3>
                    <p class="small text-muted"><?= Security::clean(mb_strimwidth($menu['description'], 0, 110, '…')) ?></p>
                    <p class="mb-1"><strong><?= number_format($menu['prix_par_personne'], 2) ?> € / personne</strong></p>
                    <p class="small text-muted">À partir de <?= (int) $menu['nombre_personnes_min'] ?> personnes</p>
                    <a href="/menu-detail.php?id=<?= (int) $menu['menu_id'] ?>" class="btn btn-outline-primary btn-sm">Voir le détail</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="/menus.php" class="btn btn-primary">Voir tous les menus</a>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($avis)): ?>
<section class="container py-5">
    <h2 class="mb-4 text-center">Ce que nos clients en pensent</h2>
    <div class="row g-4">
        <?php foreach ($avis as $a): ?>
        <div class="col-md-4">
            <div class="avis-card">
                <p class="mb-1">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi <?= $i <= $a['note'] ? 'bi-star-fill text-warning' : 'bi-star' ?>" aria-hidden="true"></i>
                    <?php endfor; ?>
                    <span class="visually-hidden"><?= (int) $a['note'] ?> sur 5</span>
                </p>
                <p class="fst-italic">« <?= Security::clean($a['commentaire']) ?> »</p>
                <p class="small text-muted mb-0">— <?= Security::clean($a['prenom']) ?> <?= Security::clean(substr($a['nom'],0,1)) ?>.</p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
