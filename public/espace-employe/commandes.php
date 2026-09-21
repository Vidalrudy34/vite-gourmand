<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('employe', 'administrateur');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    $id = (int) $_POST['commande_id'];
    $nouveauStatut = $_POST['statut'];
    $motif = trim($_POST['motif'] ?? '');
    $modeContact = trim($_POST['mode_contact'] ?? '');

    if ($nouveauStatut === 'annulee' && ($motif === '' || $modeContact === '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Pour annuler une commande, vous devez indiquer le mode de contact utilisé et le motif.'];
    } else {
        CommandeModel::updateStatut($id, $nouveauStatut, $nouveauStatut === 'annulee' ? $motif : null, $nouveauStatut === 'annulee' ? $modeContact : null);
        if (isset($_POST['materiel_prete'])) {
            CommandeModel::marquerMaterielPrete($id, true);
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le statut de la commande a été mis à jour.'];
    }
    header('Location: /espace-employe/commandes.php' . (!empty($_GET) ? '?' . http_build_query($_GET) : ''));
    exit;
}

$filters = ['statut' => $_GET['statut'] ?? null, 'client' => $_GET['client'] ?? null];
$commandes = CommandeModel::findAll($filters);

$statuts = [
    'en_attente' => 'En attente', 'accepte' => 'Acceptée', 'en_preparation' => 'En préparation',
    'en_cours_de_livraison' => 'En cours de livraison', 'livre' => 'Livrée',
    'en_attente_retour_materiel' => 'En attente de retour du matériel', 'terminee' => 'Terminée', 'annulee' => 'Annulée',
];

$pageTitle = 'Gestion des commandes';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <h1>Gestion des commandes</h1>

    <form method="get" class="row g-3 mb-4 bg-white p-3 rounded shadow-sm">
        <div class="col-md-4">
            <label class="form-label">Filtrer par statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <?php foreach ($statuts as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($filters['statut'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Rechercher un client</label>
            <input type="text" name="client" class="form-control" value="<?= Security::clean($filters['client'] ?? '') ?>" placeholder="Nom, prénom ou mail">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-hover bg-white align-middle">
        <thead><tr><th>N°</th><th>Client</th><th>Menu</th><th>Date prestation</th><th>Total</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($commandes as $c): ?>
        <tr>
            <td><?= Security::clean($c['numero_commande']) ?></td>
            <td><?= Security::clean($c['prenom'] . ' ' . $c['nom']) ?><br><span class="small text-muted"><?= Security::clean($c['telephone']) ?> / <?= Security::clean($c['email']) ?></span></td>
            <td><?= Security::clean($c['menu_titre']) ?></td>
            <td><?= date('d/m/Y', strtotime($c['date_prestation'])) ?></td>
            <td><?= number_format((float) $c['prix_total'], 2) ?> €</td>
            <td><span class="badge bg-secondary"><?= $statuts[$c['statut']] ?? $c['statut'] ?></span></td>
            <td>
                <?php if (!in_array($c['statut'], ['terminee', 'annulee'], true)): ?>
                <form method="post" class="statut-form" data-id="<?= (int) $c['commande_id'] ?>">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="commande_id" value="<?= (int) $c['commande_id'] ?>">
                    <select name="statut" class="form-select form-select-sm mb-1 select-statut">
                        <?php foreach ($statuts as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $c['statut'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="annulation-fields d-none">
                        <input type="text" name="mode_contact" class="form-control form-control-sm mb-1" placeholder="Mode de contact (appel, mail...)">
                        <textarea name="motif" class="form-control form-control-sm mb-1" placeholder="Motif de l'annulation"></textarea>
                    </div>
                    <div class="form-check form-check-sm mb-1">
                        <input class="form-check-input" type="checkbox" name="materiel_prete" id="mat<?= (int) $c['commande_id'] ?>" <?= $c['materiel_prete'] ? 'checked disabled' : '' ?>>
                        <label class="form-check-label small" for="mat<?= (int) $c['commande_id'] ?>">Matériel prêté</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Mettre à jour</button>
                </form>
                <?php else: ?>
                    <span class="text-muted small">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<script>
document.querySelectorAll('.statut-form').forEach(form => {
    const select = form.querySelector('.select-statut');
    const fields = form.querySelector('.annulation-fields');
    function toggle() { fields.classList.toggle('d-none', select.value !== 'annulee'); }
    select.addEventListener('change', toggle);
    toggle();
});
</script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
