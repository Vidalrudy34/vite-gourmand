<?php
require_once __DIR__ . '/../src/bootstrap.php';

$pageTitle = 'Nos menus';
$themes = MenuModel::allThemes();
$regimes = MenuModel::allRegimes();

require __DIR__ . '/partials/header.php';
?>
<div class="container py-4">
    <h1 class="mb-4">Nos menus</h1>

    <form id="filtres-form" class="row g-3 mb-4 bg-white p-3 rounded shadow-sm" aria-label="Filtrer les menus">
        <div class="col-md-2">
            <label for="prix_min" class="form-label">Prix min (€)</label>
            <input type="number" min="0" step="0.5" class="form-control" id="prix_min" name="prix_min">
        </div>
        <div class="col-md-2">
            <label for="prix_max" class="form-label">Prix max (€)</label>
            <input type="number" min="0" step="0.5" class="form-control" id="prix_max" name="prix_max">
        </div>
        <div class="col-md-3">
            <label for="theme_id" class="form-label">Thème</label>
            <select class="form-select" id="theme_id" name="theme_id">
                <option value="">Tous</option>
                <?php foreach ($themes as $t): ?>
                    <option value="<?= (int) $t['theme_id'] ?>"><?= Security::clean($t['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="regime_id" class="form-label">Régime</label>
            <select class="form-select" id="regime_id" name="regime_id">
                <option value="">Tous</option>
                <?php foreach ($regimes as $r): ?>
                    <option value="<?= (int) $r['regime_id'] ?>"><?= Security::clean($r['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="personnes_min" class="form-label">Nb. personnes min.</label>
            <input type="number" min="1" class="form-control" id="personnes_min" name="personnes_min">
        </div>
    </form>

    <div id="resultats-menus" aria-live="polite">
        <p class="text-muted">Chargement des menus…</p>
    </div>
</div>

<script>
function chargerMenus() {
    const form = document.getElementById('filtres-form');
    const params = new URLSearchParams(new FormData(form)).toString();
    fetch('/api/filtres-menus.php?' + params)
        .then(r => r.text())
        .then(html => { document.getElementById('resultats-menus').innerHTML = html; });
}
document.getElementById('filtres-form').addEventListener('input', chargerMenus);
document.addEventListener('DOMContentLoaded', chargerMenus);
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
