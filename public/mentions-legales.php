<?php
require_once __DIR__ . '/../src/bootstrap.php';
$pageTitle = 'Mentions légales';
require __DIR__ . '/partials/header.php';
?>
<div class="container py-4">
    <h1>Mentions légales</h1>
    <h2 class="h5 mt-4">Éditeur du site</h2>
    <p>Vite &amp; Gourmand — Entreprise de restauration événementielle<br>
    Siège social : Bordeaux (33000), France<br>
    Contact : contact@vitegourmand.fr</p>

    <h2 class="h5 mt-4">Hébergement</h2>
    <p>Ce site est hébergé par un prestataire d'hébergement web (voir la documentation technique du projet pour le détail de l'hébergeur retenu).</p>

    <h2 class="h5 mt-4">Protection des données personnelles (RGPD)</h2>
    <p>Les données collectées lors de la création de compte et de la commande (nom, prénom, adresse, téléphone, mail)
    sont utilisées exclusivement pour la gestion de votre compte et de vos commandes. Elles ne sont ni cédées, ni
    vendues à des tiers. Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression
    de vos données, à exercer en nous contactant via la <a href="/contact.php">page de contact</a>.</p>
    <p>Les mots de passe sont stockés de façon chiffrée (hachage) et ne sont jamais accessibles en clair, y compris
    par notre équipe.</p>

    <h2 class="h5 mt-4">Propriété intellectuelle</h2>
    <p>L'ensemble des contenus présents sur ce site (textes, images, logo) est la propriété de Vite &amp; Gourmand,
    sauf mention contraire.</p>

    <h2 class="h5 mt-4">Accessibilité</h2>
    <p>Ce site a été conçu en cherchant à respecter les recommandations du RGAA (Référentiel Général d'Amélioration
    de l'Accessibilité).</p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
