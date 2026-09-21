<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('utilisateur');
$user = Auth::user();

$id = (int) ($_GET['id'] ?? 0);
$commande = CommandeModel::find($id);
if (!$commande || (int) $commande['utilisateur_id'] !== (int) $user['id']) {
    http_response_code(404);
    die('Commande introuvable.');
}

$errors = [];
$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    if ($action === 'annuler') {
        if (CommandeModel::annuler($id, $user['id'])) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre commande a été annulée.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cette commande ne peut plus être annulée (déjà acceptée par notre équipe).'];
        }
        header('Location: /espace-utilisateur/commande-detail.php?id=' . $id);
        exit;
    }

    if ($action === 'modifier' && $commande['statut'] === 'en_attente') {
        $menu = MenuModel::find($commande['menu_id']);
        $nbPersonnes = (int) $_POST['nombre_personnes'];
        $ville = trim($_POST['ville_livraison']);
        $distance = (float) $_POST['distance_km'];
        if ($nbPersonnes < (int) $menu['nombre_personnes_min']) {
            $errors[] = 'Le nombre de personnes doit être au moins ' . $menu['nombre_personnes_min'] . '.';
        } else {
            $calcul = Price::calculerCommande((float) $menu['prix_par_personne'], $nbPersonnes, (int) $menu['nombre_personnes_min'], $ville, $distance);
            CommandeModel::modifier($id, $user['id'], [
                'date_prestation' => $_POST['date_prestation'],
                'heure_livraison' => $_POST['heure_livraison'],
                'adresse_livraison' => trim($_POST['adresse_livraison']),
                'ville_livraison' => $ville,
                'code_postal_livraison' => trim($_POST['code_postal_livraison']),
                'distance_km' => $distance,
                'nombre_personnes' => $nbPersonnes,
                'prix_menu' => $calcul['prix_menu'],
                'prix_livraison' => $calcul['frais_livraison'],
                'remise' => $calcul['remise'],
                'prix_total' => $calcul['prix_total'],
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre commande a été mise à jour.'];
            header('Location: /espace-utilisateur/commande-detail.php?id=' . $id);
            exit;
        }
    }

    if ($action === 'avis' && $commande['statut'] === 'terminee' && !AvisModel::existsForCommande($id)) {
        $note = (int) $_POST['note'];
        $commentaire = trim($_POST['commentaire']);
        if ($note < 1 || $note > 5 || $commentaire === '') {
            $errors[] = 'Merci de donner une note (1 à 5) et un commentaire.';
        } else {
            AvisModel::create($id, $user['id'], $note, $commentaire);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Merci pour votre avis, il sera visible après validation par notre équipe.'];
            header('Location: /espace-utilisateur/commande-detail.php?id=' . $id);
            exit;
        }
    }
}

$commande = CommandeModel::find($id); // refresh
$labelsStatut = [
    'en_attente' => 'En attente', 'accepte' => 'Acceptée', 'en_preparation' => 'En préparation',
    'en_cours_de_livraison' => 'En cours de livraison', 'livre' => 'Livrée',
    'en_attente_retour_materiel' => 'En attente de retour du matériel', 'terminee' => 'Terminée', 'annulee' => 'Annulée',
];

$pageTitle = 'Commande ' . $commande['numero_commande'];
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <h1>Commande <?= Security::clean($commande['numero_commande']) ?></h1>
    <p>Menu : <strong><?= Security::clean($commande['menu_titre']) ?></strong> — Statut :
        <span class="badge bg-secondary"><?= $labelsStatut[$commande['statut']] ?? $commande['statut'] ?></span>
    </p>

    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h6">Détails</h2>
                    <p class="mb-1">Date de prestation : <?= date('d/m/Y', strtotime($commande['date_prestation'])) ?> à <?= substr($commande['heure_livraison'],0,5) ?></p>
                    <p class="mb-1">Adresse : <?= Security::clean($commande['adresse_livraison']) ?>, <?= Security::clean($commande['code_postal_livraison']) ?> <?= Security::clean($commande['ville_livraison']) ?></p>
                    <p class="mb-1">Nombre de personnes : <?= (int) $commande['nombre_personnes'] ?></p>
                    <p class="mb-1">Prix du menu : <?= number_format((float) $commande['prix_menu'],2) ?> €</p>
                    <p class="mb-1">Remise : <?= number_format((float) $commande['remise'],2) ?> €</p>
                    <p class="mb-1">Frais de livraison : <?= number_format((float) $commande['prix_livraison'],2) ?> €</p>
                    <p class="mb-0 fs-5">Total : <strong><?= number_format((float) $commande['prix_total'],2) ?> €</strong></p>
                </div>
            </div>

            <?php if ($commande['statut'] === 'en_attente'): ?>
            <form method="post" class="d-inline">
                <?= Security::csrfField() ?>
                <input type="hidden" name="action" value="annuler">
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Confirmer l\'annulation de cette commande ?');">Annuler la commande</button>
            </form>

            <div class="card mt-3">
                <div class="card-body">
                    <h2 class="h6">Modifier ma commande</h2>
                    <form method="post">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="action" value="modifier">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Date de prestation</label>
                                <input type="date" name="date_prestation" class="form-control" value="<?= $commande['date_prestation'] ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Heure de livraison</label>
                                <input type="time" name="heure_livraison" class="form-control" value="<?= substr($commande['heure_livraison'],0,5) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="adresse_livraison" class="form-control" value="<?= Security::clean($commande['adresse_livraison']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville_livraison" class="form-control" value="<?= Security::clean($commande['ville_livraison']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code postal</label>
                                <input type="text" name="code_postal_livraison" class="form-control" value="<?= Security::clean($commande['code_postal_livraison']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Distance depuis Bordeaux (km)</label>
                                <input type="number" min="0" step="0.1" name="distance_km" class="form-control" value="<?= (float) $commande['distance_km'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre de personnes</label>
                                <input type="number" min="<?= (int) $commande['nombre_personnes_min'] ?>" name="nombre_personnes" class="form-control" value="<?= (int) $commande['nombre_personnes'] ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <?php if (in_array($commande['statut'], ['accepte','en_preparation','en_cours_de_livraison','livre','en_attente_retour_materiel','terminee'], true)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h6">Suivi de la commande</h2>
                    <ol class="list-group list-group-numbered">
                        <?php foreach ($commande['historique'] as $h): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <span><?= $labelsStatut[$h['statut']] ?? $h['statut'] ?></span>
                                <span class="small text-muted"><?= date('d/m/Y H:i', strtotime($h['date_modification'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($commande['statut'] === 'en_attente_retour_materiel'): ?>
                <div class="alert alert-warning">Du matériel vous a été prêté pour cette prestation. Merci de le
                restituer avant le <?= date('d/m/Y', strtotime($commande['date_limite_restitution'])) ?> (contactez-nous
                pour organiser la restitution), sans quoi des frais de 600 € seront appliqués (voir nos
                <a href="/cgv.php">CGV</a>).</div>
            <?php endif; ?>

            <?php if ($commande['statut'] === 'terminee'): ?>
                <?php if (AvisModel::existsForCommande($id)): ?>
                    <div class="alert alert-info">Merci, votre avis a bien été enregistré.</div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <h2 class="h6">Donner votre avis</h2>
                        <form method="post">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="action" value="avis">
                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <select name="note" class="form-select" required>
                                    <option value="">Choisir…</option>
                                    <?php for ($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?> / 5</option><?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer mon avis</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
