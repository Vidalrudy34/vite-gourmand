<?php
require_once __DIR__ . '/../src/bootstrap.php';

Auth::requireRole('utilisateur');
$user = Auth::user();

$menuId = (int) ($_GET['menu_id'] ?? $_POST['menu_id'] ?? 0);
$menu = MenuModel::find($menuId);
if (!$menu) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Menu introuvable.'];
    header('Location: /menus.php');
    exit;
}

$errors = [];
$config = require __DIR__ . '/../src/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::checkCsrf()) {
        $errors[] = 'Session expirée, merci de réessayer.';
    } else {
        $datePrestation = $_POST['date_prestation'] ?? '';
        $heureLivraison = $_POST['heure_livraison'] ?? '';
        $adresse = trim($_POST['adresse_livraison'] ?? '');
        $ville = trim($_POST['ville_livraison'] ?? '');
        $cp = trim($_POST['code_postal_livraison'] ?? '');
        $distance = (float) ($_POST['distance_km'] ?? 0);
        $nbPersonnes = (int) ($_POST['nombre_personnes'] ?? 0);

        if (strtotime($datePrestation) === false || strtotime($datePrestation) < strtotime('today')) {
            $errors[] = 'Merci de choisir une date de prestation valide (à partir d\'aujourd\'hui).';
        }
        if ($heureLivraison === '' || $adresse === '' || $ville === '' || $cp === '') {
            $errors[] = 'Merci de renseigner tous les champs de livraison.';
        }
        if ($nbPersonnes < (int) $menu['nombre_personnes_min']) {
            $errors[] = 'Le nombre de personnes doit être au moins égal au minimum requis par ce menu (' . $menu['nombre_personnes_min'] . ').';
        }
        if ((int) $menu['quantite_disponible'] <= 0) {
            $errors[] = 'Ce menu n\'est plus disponible.';
        }

        if (empty($errors)) {
            $calcul = Price::calculerCommande(
                (float) $menu['prix_par_personne'], $nbPersonnes, (int) $menu['nombre_personnes_min'], $ville, $distance
            );

            $commandeId = CommandeModel::create([
                'utilisateur_id' => $user['id'],
                'menu_id' => $menu['menu_id'],
                'date_prestation' => $datePrestation,
                'heure_livraison' => $heureLivraison,
                'adresse_livraison' => $adresse,
                'ville_livraison' => $ville,
                'code_postal_livraison' => $cp,
                'distance_km' => $distance,
                'nombre_personnes' => $nbPersonnes,
                'prix_menu' => $calcul['prix_menu'],
                'prix_livraison' => $calcul['frais_livraison'],
                'remise' => $calcul['remise'],
                'prix_total' => $calcul['prix_total'],
            ]);

            MenuModel::decrementerStock($menu['menu_id']);
            StatsModel::enregistrer($commandeId, $menu['menu_id'], $menu['titre'], $calcul['prix_total'], $nbPersonnes);

            Mailer::send($user['email'], 'Confirmation de votre commande Vite & Gourmand',
                "Bonjour {$user['prenom']},\n\nVotre commande pour le menu \"{$menu['titre']}\" a bien été enregistrée.\n" .
                "Date de prestation : $datePrestation\nNombre de personnes : $nbPersonnes\nTotal : {$calcul['prix_total']} €\n\n" .
                "Vous pouvez suivre l'état de votre commande depuis votre espace personnel.\n\nMerci de votre confiance !");

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre commande a été enregistrée avec succès.'];
            header('Location: /espace-utilisateur/commande-detail.php?id=' . $commandeId);
            exit;
        }
    }
}

$pageTitle = 'Commander : ' . $menu['titre'];
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4" style="max-width: 720px;">
    <h1>Commander « <?= Security::clean($menu['titre']) ?> »</h1>
    <p class="text-muted">Minimum <?= (int) $menu['nombre_personnes_min'] ?> personnes — <?= number_format((float) $menu['prix_par_personne'], 2) ?> € / personne</p>

    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>

    <form method="post" id="commande-form">
        <?= Security::csrfField() ?>
        <input type="hidden" name="menu_id" value="<?= (int) $menu['menu_id'] ?>">

        <fieldset class="mb-4">
            <legend class="h5">Vos informations</legend>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Nom</label><input type="text" class="form-control" value="<?= Security::clean($user['nom']) ?>" disabled></div>
                <div class="col-md-4"><label class="form-label">Prénom</label><input type="text" class="form-control" value="<?= Security::clean($user['prenom']) ?>" disabled></div>
                <div class="col-md-4"><label class="form-label">Mail</label><input type="text" class="form-control" value="<?= Security::clean($user['email']) ?>" disabled></div>
                <div class="col-md-6"><label class="form-label">GSM</label><input type="text" class="form-control" value="<?= Security::clean($user['telephone']) ?>" disabled></div>
            </div>
        </fieldset>

        <fieldset class="mb-4">
            <legend class="h5">Prestation</legend>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="date_prestation" class="form-label">Date de la prestation *</label>
                    <input type="date" class="form-control" id="date_prestation" name="date_prestation" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-6">
                    <label for="heure_livraison" class="form-label">Heure de livraison souhaitée *</label>
                    <input type="time" class="form-control" id="heure_livraison" name="heure_livraison" required>
                </div>
                <div class="col-12">
                    <label for="adresse_livraison" class="form-label">Adresse de livraison *</label>
                    <input type="text" class="form-control" id="adresse_livraison" name="adresse_livraison" required value="<?= Security::clean($user['adresse_postale']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="ville_livraison" class="form-label">Ville *</label>
                    <input type="text" class="form-control" id="ville_livraison" name="ville_livraison" required value="<?= Security::clean($user['ville']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="code_postal_livraison" class="form-label">Code postal *</label>
                    <input type="text" class="form-control" id="code_postal_livraison" name="code_postal_livraison" required value="<?= Security::clean($user['code_postal']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="distance_km" class="form-label">Distance depuis Bordeaux (km)</label>
                    <input type="number" min="0" step="0.1" class="form-control" id="distance_km" name="distance_km" value="0">
                    <div class="form-text">Laissez à 0 si la livraison a lieu à Bordeaux (gratuite).</div>
                </div>
                <div class="col-md-6">
                    <label for="nombre_personnes" class="form-label">Nombre de personnes *</label>
                    <input type="number" min="<?= (int) $menu['nombre_personnes_min'] ?>" class="form-control" id="nombre_personnes" name="nombre_personnes" required value="<?= (int) $menu['nombre_personnes_min'] ?>">
                </div>
            </div>
        </fieldset>

        <div class="card bg-light mb-4">
            <div class="card-body">
                <h2 class="h6">Récapitulatif du prix</h2>
                <p class="mb-1">Prix du menu : <strong id="recap-menu">-</strong> €</p>
                <p class="mb-1">Remise éventuelle : <strong id="recap-remise">-</strong> €</p>
                <p class="mb-1">Frais de livraison : <strong id="recap-livraison">-</strong> €</p>
                <p class="mb-0 fs-5">Total : <strong id="recap-total">-</strong> €</p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Valider la commande</button>
    </form>
</div>

<script>
const prixParPersonne = <?= (float) $menu['prix_par_personne'] ?>;
const minimumMenu = <?= (int) $menu['nombre_personnes_min'] ?>;

function recalculer() {
    const nb = parseInt(document.getElementById('nombre_personnes').value || '0', 10);
    const ville = document.getElementById('ville_livraison').value.trim().toLowerCase();
    const distance = parseFloat(document.getElementById('distance_km').value || '0');

    const prixMenu = prixParPersonne * nb;
    const remise = (nb >= minimumMenu + 5) ? prixMenu * 0.10 : 0;
    const livraison = (ville === 'bordeaux') ? 0 : (5 + 0.59 * Math.max(0, distance));
    const total = prixMenu - remise + livraison;

    document.getElementById('recap-menu').textContent = prixMenu.toFixed(2);
    document.getElementById('recap-remise').textContent = remise.toFixed(2);
    document.getElementById('recap-livraison').textContent = livraison.toFixed(2);
    document.getElementById('recap-total').textContent = total.toFixed(2);
}
['nombre_personnes', 'ville_livraison', 'distance_km'].forEach(id => {
    document.getElementById(id).addEventListener('input', recalculer);
});
document.addEventListener('DOMContentLoaded', recalculer);
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
