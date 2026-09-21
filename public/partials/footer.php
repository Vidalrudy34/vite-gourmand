<?php $horaires = HoraireModel::all(); ?>
</main>
<footer class="text-light mt-5 py-4" aria-label="Pied de page">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h2 class="h6 text-uppercase">Vite &amp; Gourmand</h2>
                <p class="small mb-0">Traiteur événementiel à Bordeaux depuis 25 ans.</p>
            </div>
            <div class="col-md-4">
                <h2 class="h6 text-uppercase">Horaires</h2>
                <ul class="list-unstyled small mb-0">
                    <?php foreach ($horaires as $h): ?>
                        <li>
                            <?= ucfirst($h['jour']) ?> :
                            <?= $h['ferme'] ? 'Fermé' : Security::clean(substr($h['heure_ouverture'],0,5)) . ' - ' . Security::clean(substr($h['heure_fermeture'],0,5)) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h2 class="h6 text-uppercase">Informations</h2>
                <ul class="list-unstyled small mb-0">
                    <li><a class="link-light" href="/mentions-legales.php">Mentions légales</a></li>
                    <li><a class="link-light" href="/cgv.php">Conditions générales de vente</a></li>
                    <li><a class="link-light" href="/contact.php">Nous contacter</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-light">
        <p class="small mb-0 text-center">&copy; <?= date('Y') ?> Vite &amp; Gourmand — Tous droits réservés.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
