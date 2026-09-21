<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('administrateur');

$menuId = !empty($_GET['menu_id']) ? (int) $_GET['menu_id'] : null;
$dateDebut = $_GET['date_debut'] ?? null;
$dateFin = $_GET['date_fin'] ?? null;

$nombreParMenu = StatsModel::nombreCommandesParMenu();
$caParMenu = StatsModel::chiffreAffairesParMenu($menuId, $dateDebut, $dateFin);
$menus = MenuModel::findAllWithFilters([]);
$mongoOk = MongoDatabase::isAvailable();

$pageTitle = 'Espace administrateur';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Espace administrateur</h1>
        <a href="/espace-admin/employes.php" class="btn btn-primary">Gérer les employés</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="/espace-employe/commandes.php">Commandes</a></div>
        <div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="/espace-employe/menus.php">Menus</a></div>
        <div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="/espace-employe/avis.php">Avis</a></div>
        <div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="/espace-employe/horaires.php">Horaires</a></div>
    </div>

    <?php if (!$mongoOk): ?>
        <div class="alert alert-warning">La base MongoDB n'est pas accessible actuellement : les statistiques
        ci-dessous ne peuvent pas être calculées. Vérifiez la configuration (voir README.md).</div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="h5">Nombre de commandes par menu</h2>
                    <canvas id="chartCommandes" height="220" role="img" aria-label="Graphique du nombre de commandes par menu"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="h5">Chiffre d'affaires par menu</h2>
                    <form method="get" class="row g-2 mb-3">
                        <div class="col-4">
                            <select name="menu_id" class="form-select form-select-sm">
                                <option value="">Tous les menus</option>
                                <?php foreach ($menus as $m): ?>
                                    <option value="<?= $m['menu_id'] ?>" <?= $menuId == $m['menu_id'] ? 'selected' : '' ?>><?= Security::clean($m['titre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-3"><input type="date" name="date_debut" class="form-control form-control-sm" value="<?= Security::clean($dateDebut ?? '') ?>"></div>
                        <div class="col-3"><input type="date" name="date_fin" class="form-control form-control-sm" value="<?= Security::clean($dateFin ?? '') ?>"></div>
                        <div class="col-2"><button class="btn btn-sm btn-primary w-100">Filtrer</button></div>
                    </form>
                    <table class="table table-sm">
                        <thead><tr><th>Menu</th><th>Commandes</th><th>CA</th></tr></thead>
                        <tbody>
                        <?php foreach ($caParMenu as $row): ?>
                            <tr><td><?= Security::clean($row['menu']) ?></td><td><?= (int) $row['nb_commandes'] ?></td><td><?= number_format((float) $row['ca'], 2) ?> €</td></tr>
                        <?php endforeach; ?>
                        <?php if (empty($caParMenu)): ?><tr><td colspan="3" class="text-muted">Aucune donnée pour ces filtres.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const data = <?= json_encode($nombreParMenu) ?>;
new Chart(document.getElementById('chartCommandes'), {
    type: 'bar',
    data: {
        labels: data.map(d => d.menu),
        datasets: [{ label: 'Nombre de commandes', data: data.map(d => d.total), backgroundColor: '#C1440E' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
