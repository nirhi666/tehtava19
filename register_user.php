<?php
// register_user.php
session_start();
require_once __DIR__.'/db.php';
require_once __DIR__.'/auth.php';

$errors = [];
$username = '';

// (valinnainen) jos jo kirjautunut, ohjaa listaan
if (is_logged_in()) {
    $_SESSION['flash_errors'] = ["Olet jo kirjautunut sisään."];
    header('Location: list.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $pass     = $_POST['password']  ?? '';
    $pass2    = $_POST['password2'] ?? '';

    // Validaatio
    if ($username === '') {
        $errors[] = 'Käyttäjänimi vaaditaan.';
    } else {
        if (mb_strlen($username) < 3 || mb_strlen($username) > 60) {
            $errors[] = 'Käyttäjänimen pituus 3–60 merkkiä.';
        }
        if (!preg_match('/^[A-Za-z0-9_.-]+$/u', $username)) {
            $errors[] = 'Käyttäjänimessä sallitaan kirjaimet, numerot, ., _ ja - .';
        }
    }

    if (strlen($pass) < 8 || !preg_match('/[0-9]/', $pass) || !preg_match('/[A-Za-z]/', $pass)) {
        $errors[] = 'Salasana: väh. 8 merkkiä, oltava kirjain ja numero.';
    }
    if ($pass !== $pass2) {
        $errors[] = 'Salasanat eivät täsmää.';
    }

    // Duplikaattitarkistus
    if (!$errors) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();

        if ($cnt > 0) {
            $errors[] = 'Käyttäjänimi on varattu.';
        }
    }

    // Tallenna
    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'user')");
        $stmt->bind_param("ss", $username, $hash);
        if (!$stmt->execute()) {                           // ⭐ virhe talteen
            $errors[] = "Tallennus epäonnistui: ".$conn->error;
        }
        $stmt->close();

        if (!$errors) {
            $newId = $conn->insert_id;

            // Autologin + session id:n uudistus
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)$newId, 'username' => $username, 'role' => 'user'];

            $_SESSION['flash_success'] = "Käyttäjätili luotu ja kirjautuminen onnistui!"; // ⭐ flash
            header('Location: list.php'); // tai work_list.php jos haluat suoraan vuoroihin
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Rekisteröidy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f6f7fb; }
    .page-wrap{ max-width:640px; }
    .card{ border:0; box-shadow:0 10px 25px rgba(0,0,0,.06); border-radius:1rem; }
  </style>
</head>
<body>
<div class="container py-5 page-wrap">
  <h2 class="mb-4 text-primary">Luo käyttäjätili</h2>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body p-4">
      <form method="post" action="register_user.php" class="row g-3 needs-validation" novalidate>
        <div class="col-12">
          <label class="form-label">Käyttäjänimi *</label>
          <input class="form-control" name="username" required minlength="3" maxlength="60"
                 pattern="^[A-Za-z0-9_.-]+$"
                 value="<?= htmlspecialchars($username) ?>">
          <div class="invalid-feedback">3–60 merkkiä. Sallitut: A–Z, a–z, 0–9, . _ -</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Salasana *</label>
          <input type="password" class="form-control" name="password" required minlength="8"
                 placeholder="Väh. 8 merkkiä, kirjain + numero">
          <div class="invalid-feedback">Vähintään 8 merkkiä, oltava kirjain ja numero.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Vahvista salasana *</label>
          <input type="password" class="form-control" name="password2" required minlength="8">
          <div class="invalid-feedback">Salasanat eivät täsmää.</div>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary">Luo tili</button>
          <a class="btn btn-outline-secondary" href="login.php">Onko tili? Kirjaudu</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(()=> {
  'use strict';
  const form = document.querySelector('.needs-validation');
  form.addEventListener('submit', (e)=>{
    const fd = new FormData(form);
    const p  = (fd.get('password')  || '').toString();
    const p2 = (fd.get('password2') || '').toString();
    const passOk = p.length >= 8 && /[0-9]/.test(p) && /[A-Za-z]/.test(p);
    if (!passOk || p !== p2 || !form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    form.classList.add('was-validated');
  });
})();
</script>
</body>
</html>




