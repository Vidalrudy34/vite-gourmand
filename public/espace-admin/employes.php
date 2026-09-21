<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::requireRole('administrateur');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'creer') {
        $email = trim($_POST['email']);
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        if (!Security::isValidEmail($email)) {
            $errors[] = 'Adresse mail invalide.';
        } elseif (UtilisateurModel::findByEmail($email)) {
            $errors[] = 'Un compte existe déjà avec cette adresse mail.';
        } else {
            $motDePasseTemp = bin2hex(random_bytes(6)); // mot de passe temporaire, communiqué en direct par l'administrateur
            UtilisateurModel::createEmploye($email, $motDePasseTemp, $nom, $prenom);
            Mailer::send($email, 'Création de votre compte Vite & Gourmand',
                "Bonjour $prenom,\n\nUn compte employé a été créé pour vous sur l'application Vite & Gourmand.\n" .
                "Votre identifiant : $email\nVotre mot de passe ne vous est pas communiqué par mail : rapprochez-vous de l'administrateur pour l'obtenir.\n\nCordialement.");
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Compte employé créé. Mot de passe temporaire à transmettre en direct : $motDePasseTemp"];
            header('Location: /espace-admin/employes.php');
            exit;
        }
    }

    if ($action === 'toggle') {
        $id = (int) $_POST['utilisateur_id'];
        $actif = $_POST['actif'] === '1';
        UtilisateurModel::toggleActif($id, !$actif);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le statut du compte a été mis à jour.'];
        header('Location: /espace-admin/employes.php');
        exit;
    }
}

$employes = UtilisateurModel::listEmployes();
$pageTitle = 'Gestion des employés';
require __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
    <h1>Gestion des employés</h1>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5">Créer un compte employé</h2>
            <form method="post" class="row g-3">
                <?= Security::csrfField() ?>
                <input type="hidden" name="action" value="creer">
                <div class="col-md-4"><input type="text" name="prenom" class="form-control" placeholder="Prénom" required></div>
                <div class="col-md-4"><input type="text" name="nom" class="form-control" placeholder="Nom" required></div>
                <div class="col-md-4"><input type="email" name="email" class="form-control" placeholder="Adresse mail" required></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Créer le compte</button></div>
            </form>
            <p class="small text-muted mt-2">Le mot de passe temporaire généré ne sera jamais envoyé par mail : il vous
            sera affiché une seule fois après la création, à transmettre en direct à l'employé.</p>
        </div>
    </div>

    <table class="table table-hover bg-white">
        <thead><tr><th>Nom</th><th>Email</th><th>Statut</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($employes as $e): ?>
        <tr>
            <td><?= Security::clean($e['prenom'] . ' ' . $e['nom']) ?></td>
            <td><?= Security::clean($e['email']) ?></td>
            <td><span class="badge bg-<?= $e['actif'] ? 'success' : 'secondary' ?>"><?= $e['actif'] ? 'Actif' : 'Désactivé' ?></span></td>
            <td>
                <form method="post">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="utilisateur_id" value="<?= (int) $e['utilisateur_id'] ?>">
                    <input type="hidden" name="actif" value="<?= (int) $e['actif'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-<?= $e['actif'] ? 'danger' : 'success' ?>">
                        <?= $e['actif'] ? 'Désactiver' : 'Réactiver' ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
