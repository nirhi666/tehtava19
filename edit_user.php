<?php
require __DIR__.'/auth.php';
require_login();
require_admin();
require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Virheellinen ID");

// Hae käyttäjä
$stmt = $conn->prepare("SELECT id, username, role, ika, email, postinumero FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) die("Käyttäjää ei löydy");

function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Muokkaa käyttäjää</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:700px">
  <h2 class="mb-4 text-primary">Muokkaa käyttäjää</h2>

  <div class="card">
    <div class="card-body p-4">
      <form method="post" action="update.php" class="row g-3 needs-validation" novalidate>
        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">

        <div class="col-md-6">
          <label class="form-label">Käyttäjänimi *</label>
          <input type="text" name="username" class="form-control" required minlength="3" maxlength="60"
                 value="<?= esc($row['username']) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Rooli</label>
          <select name="role" class="form-select">
            <option value="user" <?= $row['role']==='user'?'selected':'' ?>>User</option>
            <option value="admin" <?= $row['role']==='admin'?'selected':'' ?>>Admin</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Ikä *</label>
          <input type="number" name="ika" class="form-control" required min="0" max="118"
                 value="<?= esc($row['ika']) ?>">
        </div>

        <div class="col-md-5">
          <label class="form-label">Sähköposti *</label>
          <input type="email" name="email" class="form-control" required maxlength="150"
                 value="<?= esc($row['email']) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Postinumero *</label>
          <input type="text" name="postinumero" class="form-control" required pattern="^\d{5}$"
                 value="<?= esc($row['postinumero']) ?>">
        </div>

        <div class="col-12 d-flex gap-2 mt-3">
          <button class="btn btn-primary">Tallenna muutokset</button>
          <a href="list.php" class="btn btn-outline-secondary">Takaisin listaan</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
// Perus client-validointi Bootstrapille
(()=> {
  'use strict';
  const form = document.querySelector('.needs-validation');
  form.addEventListener('submit', (e)=> {
    if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    form.classList.add('was-validated');
  }, false);
})();
</script>
</body>
</html>


