<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('employe', 'administrateur');

$id = (int) ($_GET['id'] ?? 0);
$menu = $id ? MenuModel::find($id) : null;
$themes = MenuModel::allThemes();
$regimes = MenuModel::allRegimes();
$plats = MenuModel::allPlats();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    $data = [
        'titre' => trim($_POST['titre']),
        'description' => trim($_POST['description']),
        'theme_id' => (int) $_POST['theme_id'],
        'regime_id' => (int) $_POST['regime_id'],
        'nombre_personnes_min' => (int) $_POST['nombre_personnes_min'],
        'prix_par_personne' => (float) $_POST['prix_par_personne'],
        'conditions' => trim($_POST['conditions']),
        'quantite_disponible' => (int) $_POST['quantite_disponible'],
    ];
    $platIds = $_POST['plats'] ?? [];

    if ($data['titre'] === '' || $data['description'] === '') {
        $errors[] = 'Le titre et la description sont obligatoires.';
    }

    if (empty($errors)) {
        if ($menu) {
            MenuModel::update($id, $data);
            $menuId = $id;
        } else {
            $menuId = MenuModel::create($data);
        }
        MenuModel::attachPlats($menuId, $platIds);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le menu a été enregistré.'];
        header('Location: /espace-employe/menus.php');
        exit;
    }
}

$platsSelectionnes = $menu ? array_column($menu['plats'], 'plat_id') : [];
$pageTitle = $menu ? 'Modifier un menu' : 'Nouveau menu';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4" style="max-width: 720px;">
    <h1><?= $menu ? 'Modifier le menu' : 'Nouveau menu' ?></h1>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>

    <form method="post">
        <?= Security::csrfField() ?>
        <div class="mb-3">
            <label class="form-label">Titre *</label>
            <input type="text" name="titre" class="form-control" required value="<?= Security::clean($menu['titre'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control" rows="3" required><?= Security::clean($menu['description'] ?? '') ?></textarea>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Thème *</label>
                <select name="theme_id" class="form-select" required>
                    <?php foreach ($themes as $t): ?>
                        <option value="<?= $t['theme_id'] ?>" <?= (($menu['theme_id'] ?? null) == $t['theme_id']) ? 'selected' : '' ?>><?= Security::clean($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Régime *</label>
                <select name="regime_id" class="form-select" required>
                    <?php foreach ($regimes as $r): ?>
                        <option value="<?= $r['regime_id'] ?>" <?= (($menu['regime_id'] ?? null) == $r['regime_id']) ? 'selected' : '' ?>><?= Security::clean($r['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nombre de personnes minimum *</label>
                <input type="number" min="1" name="nombre_personnes_min" class="form-control" required value="<?= (int) ($menu['nombre_personnes_min'] ?? 1) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Prix par personne (€) *</label>
                <input type="number" min="0" step="0.01" name="prix_par_personne" class="form-control" required value="<?= Security::clean((string) ($menu['prix_par_personne'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Stock disponible *</label>
                <input type="number" min="0" name="quantite_disponible" class="form-control" required value="<?= (int) ($menu['quantite_disponible'] ?? 0) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Conditions (délai de commande, précautions de stockage...)</label>
                <textarea name="conditions" class="form-control" rows="2"><?= Security::clean($menu['conditions'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Plats composant le menu</label>
                <div class="row">
                <?php foreach ($plats as $p): ?>
                    <div class="col-md-4 form-check">
                        <input class="form-check-input" type="checkbox" name="plats[]" value="<?= $p['plat_id'] ?>" id="plat<?= $p['plat_id'] ?>" <?= in_array($p['plat_id'], $platsSelectionnes) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="plat<?= $p['plat_id'] ?>"><?= Security::clean($p['nom']) ?> (<?= $p['type'] ?>)</label>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Enregistrer</button>
        <a href="/espace-employe/menus.php" class="btn btn-outline-secondary mt-4">Annuler</a>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
