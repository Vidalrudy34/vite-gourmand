<?php
require_once __DIR__ . '/../src/bootstrap.php';

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Security::checkCsrf()) {
    $email = trim($_POST['email'] ?? '');
    $user = UtilisateurModel::findByEmail($email);
    // On ne révèle jamais si l'adresse existe ou non (sécurité)
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
        UtilisateurModel::setResetToken($email, $token, $expire);
        $config = require __DIR__ . '/../src/config/config.php';
        $lien = $config['app']['url'] . '/reinitialiser-mot-de-passe.php?token=' . $token;
        Mailer::send($email, 'Réinitialisation de votre mot de passe', "Bonjour,\n\nPour réinitialiser votre mot de passe, cliquez sur le lien suivant (valable 1 heure) :\n$lien\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez ce mail.");
    }
    $message = 'Si un compte existe avec cette adresse, un mail de réinitialisation vient de vous être envoyé.';
}

$pageTitle = 'Mot de passe oublié';
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4" style="max-width: 480px;">
    <h1>Mot de passe oublié</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= Security::clean($message) ?></div>
    <?php else: ?>
        <p>Indiquez votre adresse mail, vous recevrez un lien pour réinitialiser votre mot de passe.</p>
        <form method="post">
            <?= Security::csrfField() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
        </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
