<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('employe', 'administrateur');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    foreach ($_POST['horaires'] as $id => $h) {
        $ferme = isset($h['ferme']);
        HoraireModel::update((int) $id, $h['ouverture'] ?? null, $h['fermeture'] ?? null, $ferme);
    }
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Les horaires ont été mis à jour.'];
    header('Location: /espace-employe/horaires.php');
    exit;
}

$horaires = HoraireModel::all();
$pageTitle = 'Gestion des horaires';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4" style="max-width: 720px;">
    <h1>Horaires d'ouverture</h1>
    <form method="post">
        <?= Security::csrfField() ?>
        <?php foreach ($horaires as $h): ?>
        <div class="row g-2 align-items-center border-bottom py-2">
            <div class="col-md-3 fw-bold text-capitalize"><?= Security::clean($h['jour']) ?></div>
            <div class="col-md-3">
                <input type="time" name="horaires[<?= $h['horaire_id'] ?>][ouverture]" class="form-control" value="<?= substr($h['heure_ouverture'] ?? '',0,5) ?>">
            </div>
            <div class="col-md-3">
                <input type="time" name="horaires[<?= $h['horaire_id'] ?>][fermeture]" class="form-control" value="<?= substr($h['heure_fermeture'] ?? '',0,5) ?>">
            </div>
            <div class="col-md-3 form-check">
                <input type="checkbox" class="form-check-input" id="ferme<?= $h['horaire_id'] ?>" name="horaires[<?= $h['horaire_id'] ?>][ferme]" <?= $h['ferme'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="ferme<?= $h['horaire_id'] ?>">Fermé</label>
            </div>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary mt-4">Enregistrer</button>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
