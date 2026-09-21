<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('employe', 'administrateur');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    AvisModel::updateStatut((int) $_POST['avis_id'], $_POST['decision'] === 'valider' ? 'valide' : 'refuse');
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'L\'avis a été traité.'];
    header('Location: /espace-employe/avis.php');
    exit;
}

$avis = AvisModel::findEnAttente();
$pageTitle = 'Modération des avis';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <h1>Avis en attente de modération</h1>
    <?php if (empty($avis)): ?>
        <p class="text-muted">Aucun avis en attente.</p>
    <?php else: ?>
        <?php foreach ($avis as $a): ?>
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-1"><strong><?= Security::clean($a['prenom'] . ' ' . $a['nom']) ?></strong> — <?= Security::clean($a['menu_titre']) ?> — <?= (int) $a['note'] ?>/5</p>
                <p class="fst-italic">« <?= Security::clean($a['commentaire']) ?> »</p>
                <form method="post" class="d-flex gap-2">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="avis_id" value="<?= (int) $a['avis_id'] ?>">
                    <button type="submit" name="decision" value="valider" class="btn btn-success btn-sm">Valider</button>
                    <button type="submit" name="decision" value="refuser" class="btn btn-outline-danger btn-sm">Refuser</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
