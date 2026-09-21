<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('employe', 'administrateur');
$pageTitle = 'Espace employé';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <h1>Espace employé</h1>
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5"><i class="bi bi-clipboard-check"></i> Commandes</h2>
                <p>Suivre et mettre à jour le statut des commandes clients.</p>
                <a href="/espace-employe/commandes.php" class="btn btn-primary">Gérer les commandes</a>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5"><i class="bi bi-menu-button-wide"></i> Menus &amp; plats</h2>
                <p>Ajouter, modifier ou retirer des menus du catalogue.</p>
                <a href="/espace-employe/menus.php" class="btn btn-primary">Gérer les menus</a>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5"><i class="bi bi-star"></i> Avis clients</h2>
                <p>Valider ou refuser les avis déposés par les clients.</p>
                <a href="/espace-employe/avis.php" class="btn btn-primary">Modérer les avis</a>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5"><i class="bi bi-clock"></i> Horaires</h2>
                <p>Mettre à jour les horaires d'ouverture affichés en pied de page.</p>
                <a href="/espace-employe/horaires.php" class="btn btn-primary">Gérer les horaires</a>
            </div></div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
