<?php
require_once __DIR__ . '/../src/bootstrap.php';

$errors = [];
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::checkCsrf()) {
        $errors[] = 'Session expirée, merci de réessayer.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';
        $user = UtilisateurModel::findByEmail($email);

        if (!$user || !password_verify($mdp, $user['mot_de_passe'])) {
            $errors[] = 'Identifiants incorrects.';
        } elseif (!$user['actif']) {
            $errors[] = 'Ce compte a été désactivé. Merci de contacter l\'entreprise.';
        } else {
            Auth::login($user);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

$pageTitle = 'Connexion';
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4" style="max-width: 480px;">
    <h1>Connexion</h1>
    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= Security::clean($e) ?></div>
    <?php endforeach; ?>
    <form method="post" novalidate>
        <?= Security::csrfField() ?>
        <input type="hidden" name="redirect" value="<?= Security::clean($redirect) ?>">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>
    <p class="mt-3 text-center"><a href="/mot-de-passe-oublie.php">Mot de passe oublié ?</a></p>
    <p class="text-center">Pas encore de compte ? <a href="/inscription.php">Créer un compte</a></p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
