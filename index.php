<?php
session_start();
require __DIR__ . '/auth.php';

// Jos kirjautunut → listaan, muuten → kirjautumiseen
if (is_logged_in()) {
    header("Location: list.php");
} else {
    header("Location: login.php");
}
exit;



function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Flash-viestit
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_errors  = $_SESSION['flash_errors']  ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_errors']);

// Vanhat arvot
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<?php require 'header.php'; ?>

<h2 class="mb-4 text-primary">Rekisteröitymislomake</h2>

<?php if ($flash_success): ?>
  <div class="alert alert-success"><?= esc($flash_success) ?></div>
<?php endif; ?>
<?php if (!empty($flash_errors)): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($flash_errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card">
  <div class="card-body p-4">
    <form action="save.php" method="post" class="row g-3 needs-validation" novalidate>
      <div class="col-md-6">
        <label class="form-label">Nimi *</label>
        <input type="text" name="nimi" class="form-control" required minlength="3" maxlength="150"
               value="<?= esc($old['nimi'] ?? '') ?>">
        <div class="invalid-feedback">Nimen tulee olla 3–150 merkkiä.</div>
      </div>

      <div class="col-md-2">
        <label class="form-label">Ikä *</label>
        <input type="number" name="ika" class="form-control" required min="0" max="118"
               value="<?= esc($old['ika'] ?? '') ?>">
        <div class="invalid-feedback">Ikä pitää olla 0–118.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Sähköposti *</label>
        <input type="text" name="email" class="form-control" required maxlength="150"
               pattern="^[^\s@]+@[^\s@]+\.[^\s@]{2,}$"
               title="Anna kelvollinen sähköpostiosoite"
               value="<?= esc($old['email'] ?? '') ?>">
        <div class="invalid-feedback">Anna kelvollinen sähköpostiosoite.</div>
      </div>

      <div class="col-12">
        <label class="form-label">Osoite *</label>
        <input type="text" name="osoite" class="form-control" required maxlength="255"
               value="<?= esc($old['osoite'] ?? '') ?>">
        <div class="invalid-feedback">Osoite on pakollinen.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Postinumero *</label>
        <input type="text" name="postinumero" class="form-control" required maxlength="5" pattern="^\d{5}$"
               value="<?= esc($old['postinumero'] ?? '') ?>">
        <div class="invalid-feedback">Postinumeron tulee olla 5 numeroa.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Salasana *</label>
        <input type="password" id="salasana" name="salasana" class="form-control" required minlength="8"
               placeholder="Väh. 8 merkkiä, kirjain + numero">
        <div class="invalid-feedback">Vähintään 8 merkkiä, kirjain ja numero.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Vahvista salasana *</label>
        <input type="password" id="vahvistus" name="vahvistus" class="form-control" required minlength="8">
        <div class="invalid-feedback">Salasanat eivät täsmää.</div>
      </div>

      <div class="col-12 d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-success">Rekisteröidy</button>
        <a href="login.php" class="btn btn-outline-primary">Kirjaudu sisään</a>
      </div>
    </form>
  </div>
</div>

<script>
// Perus client-validointi + salasanatarkistus
(()=>{
  'use strict';
  const form = document.querySelector('.needs-validation');
  const pass = document.getElementById('salasana');
  const conf = document.getElementById('vahvistus');

  form.addEventListener('submit', (e)=>{
    const hasNum  = /[0-9]/.test(pass.value);
    const hasChar = /[A-Za-z]/.test(pass.value);
    pass.setCustomValidity((pass.value.length<8 || !hasNum || !hasChar) ? 'weak' : '');
    conf.setCustomValidity(pass.value !== conf.value ? 'mismatch' : '');

    if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    form.classList.add('was-validated');
  }, false);
})();
</script>

<?php require 'footer.php'; ?>


