<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('employe', 'administrateur');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf() && ($_POST['action'] ?? '') === 'supprimer') {
    MenuModel::delete((int) $_POST['menu_id']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le menu a été désactivé du catalogue.'];
    header('Location: /espace-employe/menus.php');
    exit;
}

$menus = MenuModel::findAllWithFilters([]);
$pageTitle = 'Gestion des menus';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des menus</h1>
        <a href="/espace-employe/menu-form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouveau menu</a>
    </div>

    <div class="table-responsive">
    <table class="table table-hover bg-white align-middle">
        <thead><tr><th>Titre</th><th>Thème</th><th>Régime</th><th>Prix/pers.</th><th>Min.</th><th>Stock</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($menus as $m): ?>
        <tr>
            <td><?= Security::clean($m['titre']) ?></td>
            <td><?= Security::clean($m['theme_libelle']) ?></td>
            <td><?= Security::clean($m['regime_libelle']) ?></td>
            <td><?= number_format((float) $m['prix_par_personne'],2) ?> €</td>
            <td><?= (int) $m['nombre_personnes_min'] ?></td>
            <td><?= (int) $m['quantite_disponible'] ?></td>
            <td class="text-end">
                <a href="/espace-employe/menu-form.php?id=<?= (int) $m['menu_id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                <form method="post" class="d-inline" onsubmit="return confirm('Retirer ce menu du catalogue ?');">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="menu_id" value="<?= (int) $m['menu_id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
