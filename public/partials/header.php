<?php
/** @var string $pageTitle */
$user = Auth::user();
$base = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Security::clean($pageTitle) . ' - ' : '' ?>Vite & Gourmand</title>
    <meta name="description" content="Vite & Gourmand - Traiteur à Bordeaux depuis 25 ans. Découvrez nos menus et commandez en ligne.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<a class="visually-hidden-focusable" href="#contenu-principal">Aller au contenu principal</a>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark" aria-label="Navigation principale">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/index.php">Vite &amp; Gourmand</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Ouvrir la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="/menus.php">Nos menus</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
                </ul>
                <ul class="navbar-nav">
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'administrateur'): ?>
                        <li class="nav-item"><a class="nav-link" href="/espace-admin/index.php"><i class="bi bi-speedometer2"></i> Espace admin</a></li>
                    <?php elseif ($user['role'] === 'employe'): ?>
                        <li class="nav-item"><a class="nav-link" href="/espace-employe/index.php"><i class="bi bi-briefcase"></i> Espace employé</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/espace-utilisateur/index.php"><i class="bi bi-person"></i> Mon espace</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="/deconnexion.php">Déconnexion (<?= Security::clean($user['prenom']) ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/connexion.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2 px-3" href="/inscription.php">Créer un compte</a></li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= Security::clean($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
            <?= Security::clean($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<main id="contenu-principal">
