<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('utilisateur');
$user = Auth::user();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    $data = [
        'nom' => trim($_POST['nom']), 'prenom' => trim($_POST['prenom']), 'telephone' => trim($_POST['telephone']),
        'adresse_postale' => trim($_POST['adresse_postale']), 'ville' => trim($_POST['ville']), 'code_postal' => trim($_POST['code_postal']),
    ];
    if (in_array('', $data, true)) {
        $errors[] = 'Tous les champs sont obligatoires.';
    } else {
        UtilisateurModel::updateProfil($user['id'], $data);
        $updated = UtilisateurModel::findById($user['id']);
        Auth::login($updated);
        $user = Auth::user();
        $success = true;
    }
}

$pageTitle = 'Mes informations';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4" style="max-width: 640px;">
    <h1>Mes informations personnelles</h1>
    <?php if ($success): ?><div class="alert alert-success">Vos informations ont été mises à jour.</div><?php endif; ?>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>

    <form method="post">
        <?= Security::csrfField() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= Security::clean($user['prenom']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= Security::clean($user['nom']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Mail (non modifiable)</label><input type="text" class="form-control" value="<?= Security::clean($user['email']) ?>" disabled></div>
            <div class="col-md-6"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control" value="<?= Security::clean($user['telephone']) ?>" required></div>
            <div class="col-12"><label class="form-label">Adresse postale</label><input type="text" name="adresse_postale" class="form-control" value="<?= Security::clean($user['adresse_postale']) ?>" required></div>
            <div class="col-md-8"><label class="form-label">Ville</label><input type="text" name="ville" class="form-control" value="<?= Security::clean($user['ville']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Code postal</label><input type="text" name="code_postal" class="form-control" value="<?= Security::clean($user['code_postal']) ?>" required></div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Enregistrer</button>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
