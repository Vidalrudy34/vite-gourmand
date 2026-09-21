<?php
require_once __DIR__ . '/../src/bootstrap.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$user = UtilisateurModel::findByResetToken($token);
$errors = [];

if (!$user) {
    $pageTitle = 'Lien invalide';
    require __DIR__ . '/partials/header.php';
    echo '<div class="container py-5"><div class="alert alert-danger">Ce lien de réinitialisation est invalide ou a expiré.</div><a href="/mot-de-passe-oublie.php" class="btn btn-primary">Refaire une demande</a></div>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdp2 = $_POST['mot_de_passe_confirm'] ?? '';
    if (!Security::isPasswordStrong($mdp)) {
        $errors[] = 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
    } elseif ($mdp !== $mdp2) {
        $errors[] = 'Les deux mots de passe ne correspondent pas.';
    } else {
        UtilisateurModel::updatePassword($user['utilisateur_id'], $mdp);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre mot de passe a été réinitialisé, vous pouvez vous connecter.'];
        header('Location: /connexion.php');
        exit;
    }
}

$pageTitle = 'Réinitialiser le mot de passe';
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4" style="max-width: 480px;">
    <h1>Nouveau mot de passe</h1>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>
    <form method="post">
        <?= Security::csrfField() ?>
        <input type="hidden" name="token" value="<?= Security::clean($token) ?>">
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="10">
        </div>
        <div class="mb-3">
            <label for="mot_de_passe_confirm" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required minlength="10">
        </div>
        <button type="submit" class="btn btn-primary w-100">Réinitialiser</button>
    </form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
