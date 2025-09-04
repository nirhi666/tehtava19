<?php
// login.php
session_start();
require __DIR__.'/db.php';
require __DIR__.'/auth.php';

// (valinnainen mutta hyödyllinen debug kehitykseen)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $pass     = $_POST['password'] ?? '';

    if ($username === '' || $pass === '') {
        $errors[] = 'Anna käyttäjänimi ja salasana.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $u   = $res->fetch_assoc();
        $stmt->close();

        if (!$u || !password_verify($pass, $u['password_hash'])) {
            $errors[] = 'Virheellinen käyttäjänimi tai salasana.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'       => (int)$u['id'],
                'username' => $u['username'],
                'role'     => $u['role'],
            ];
            header('Location: list.php'); // tänne kun sisään päästy
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Kirjaudu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f6f7fb; }
    .page-wrap{ max-width:560px; }
    .card{ border:0; box-shadow:0 10px 25px rgba(0,0,0,.06); border-radius:1rem; }
  </style>
</head>
<body>
<div class="container py-5 page-wrap">
  <h2 class="mb-4 text-primary">Kirjaudu sisään</h2>

  <?php if ($errors): ?>
    <div class="alert alert-danger"><?= htmlspecialchars(implode(' ', $errors)) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body p-4">
      <!-- TÄRKEÄ: action="login.php" -->
      <form method="post" action="login.php" class="row g-3 needs-validation" novalidate>
        <div class="col-12">
          <label class="form-label">Käyttäjänimi *</label>
          <input class="form-control" name="username" required minlength="3" maxlength="60"
                 value="<?= htmlspecialchars($username) ?>">
          <div class="invalid-feedback">Syötä käyttäjänimi.</div>
        </div>
        <div class="col-12">
          <label class="form-label">Salasana *</label>
          <input type="password" class="form-control" name="password" required minlength="8">
          <div class="invalid-feedback">Syötä salasana.</div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary">Kirjaudu</button>
          <a class="btn btn-outline-secondary" href="register_user.php">Luo tili</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(()=>{ // Bootstrap client-validointi
  'use strict';
  const form = document.querySelector('.needs-validation');
  form.addEventListener('submit', (e)=>{
    if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    form.classList.add('was-validated');
  }, false);
})();
</script>
</body>
</html>
