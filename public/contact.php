<?php
require_once __DIR__ . '/../src/bootstrap.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::checkCsrf()) {
        $errors[] = 'Session expirée, merci de réessayer.';
    } else {
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($titre === '' || $description === '') {
            $errors[] = 'Merci de renseigner un titre et un message.';
        }
        if (!Security::isValidEmail($email)) {
            $errors[] = 'Adresse mail invalide.';
        }

        if (empty($errors)) {
            ContactModel::create($titre, $description, $email);
            $config = require __DIR__ . '/../src/config/config.php';
            Mailer::send($config['mail']['from'], 'Nouveau message de contact : ' . $titre, "De : $email\n\n$description");
            $success = true;
        }
    }
}

$pageTitle = 'Contact';
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4" style="max-width: 640px;">
    <h1>Nous contacter</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Votre message a bien été envoyé, nous reviendrons vers vous rapidement.</div>
    <?php else: ?>
        <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= Security::clean($e) ?></div><?php endforeach; ?>
        <form method="post">
            <?= Security::csrfField() ?>
            <div class="mb-3">
                <label for="titre" class="form-label">Titre *</label>
                <input type="text" class="form-control" id="titre" name="titre" required value="<?= Security::clean($_POST['titre'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Votre message *</label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?= Security::clean($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Votre adresse mail *</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= Security::clean($_POST['email'] ?? Auth::user()['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
