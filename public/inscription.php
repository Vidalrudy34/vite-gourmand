<?php
require_once __DIR__ . '/../src/bootstrap.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::checkCsrf()) {
        $errors[] = 'Session expirée, merci de réessayer.';
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $adresse = trim($_POST['adresse_postale'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $cp = trim($_POST['code_postal'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';
        $mdp2 = $_POST['mot_de_passe_confirm'] ?? '';

        if ($nom === '' || $prenom === '' || $telephone === '' || $adresse === '' || $ville === '' || $cp === '') {
            $errors[] = 'Merci de renseigner tous les champs obligatoires.';
        }
        if (!Security::isValidEmail($email)) {
            $errors[] = 'Adresse mail invalide.';
        } elseif (UtilisateurModel::findByEmail($email)) {
            $errors[] = 'Un compte existe déjà avec cette adresse mail.';
        }
        if (!Security::isPasswordStrong($mdp)) {
            $errors[] = 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
        } elseif ($mdp !== $mdp2) {
            $errors[] = 'Les deux mots de passe ne correspondent pas.';
        }

        if (empty($errors)) {
            UtilisateurModel::create([
                'email' => $email, 'mot_de_passe' => $mdp, 'nom' => $nom, 'prenom' => $prenom,
                'telephone' => $telephone, 'adresse_postale' => $adresse, 'ville' => $ville, 'code_postal' => $cp,
            ]);

            Mailer::send($email, 'Bienvenue chez Vite & Gourmand', "Bonjour $prenom,\n\nVotre compte a bien été créé sur Vite & Gourmand. Vous pouvez dès à présent vous connecter et découvrir nos menus.\n\nÀ bientôt !\nL'équipe Vite & Gourmand");

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre compte a été créé avec succès. Vous pouvez vous connecter.'];
            header('Location: /connexion.php');
            exit;
        }
    }
}

$pageTitle = 'Créer un compte';
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4" style="max-width: 640px;">
    <h1>Créer un compte</h1>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= Security::clean($e) ?></div>
    <?php endforeach; ?>

    <form method="post" novalidate>
        <?= Security::csrfField() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="prenom" class="form-label">Prénom *</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?= Security::clean($_POST['prenom'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nom" class="form-label">Nom *</label>
                <input type="text" class="form-control" id="nom" name="nom" required value="<?= Security::clean($_POST['nom'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Adresse mail *</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= Security::clean($_POST['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="telephone" class="form-label">Numéro de GSM *</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" required value="<?= Security::clean($_POST['telephone'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label for="adresse_postale" class="form-label">Adresse postale *</label>
                <input type="text" class="form-control" id="adresse_postale" name="adresse_postale" required value="<?= Security::clean($_POST['adresse_postale'] ?? '') ?>">
            </div>
            <div class="col-md-8">
                <label for="ville" class="form-label">Ville *</label>
                <input type="text" class="form-control" id="ville" name="ville" required value="<?= Security::clean($_POST['ville'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label for="code_postal" class="form-label">Code postal *</label>
                <input type="text" class="form-control" id="code_postal" name="code_postal" required value="<?= Security::clean($_POST['code_postal'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="10">
                <div class="form-text">10 caractères min., avec majuscule, minuscule, chiffre et caractère spécial.</div>
            </div>
            <div class="col-md-6">
                <label for="mot_de_passe_confirm" class="form-label">Confirmer le mot de passe *</label>
                <input type="password" class="form-control" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required minlength="10">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Créer mon compte</button>
    </form>
    <p class="mt-3">Déjà un compte ? <a href="/connexion.php">Connectez-vous</a></p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
