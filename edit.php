<?php
require __DIR__.'/auth.php';
require_admin();
require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Virheellinen ID");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $role       = $_POST['role'] === 'admin' ? 'admin' : 'user';
    $ika        = (int)($_POST['ika'] ?? 0);
    $email      = trim($_POST['email'] ?? '');
    $postinumero= trim($_POST['postinumero'] ?? '');

    if ($username === '' || $email === '') {
        $_SESSION['flash_errors'] = ["Käyttäjänimi ja sähköposti vaaditaan."];
    } else {
        $stmt = $conn->prepare("UPDATE users 
            SET username=?, role=?, ika=?, email=?, postinumero=? 
            WHERE id=?");
        $stmt->bind_param("ssissi", $username, $role, $ika, $email, $postinumero, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash_success'] = "Käyttäjän tiedot päivitetty.";
        header("Location: list.php"); exit;
    }
}

$row = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if (!$row) die("Käyttäjää ei löydy");
function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Muokkaa käyttäjää</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:700px">
  <h2 class="mb-4 text-primary">Muokkaa käyttäjää</h2>

  <?php if (!empty($_SESSION['flash_errors'])): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach($_SESSION['flash_errors'] as $e) echo "<li>".esc($e)."</li>"; ?></ul>
    </div>
    <?php unset($_SESSION['flash_errors']); ?>
  <?php endif; ?>

  <div class="card">
    <div class="card-body p-4">
      <form method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Käyttäjänimi</label>
          <input class="form-control" name="username" required value="<?= esc($row['username']) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Rooli</label>
          <select name="role" class="form-select">
            <option value="user" <?= $row['role']==='user'?'selected':'' ?>>User</option>
            <option value="admin" <?= $row['role']==='admin'?'selected':'' ?>>Admin</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Ikä</label>
          <input type="number" name="ika" class="form-control" min="0" max="118" value="<?= esc($row['ika']) ?>">
        </div>

        <div class="col-md-5">
          <label class="form-label">Sähköposti</label>
          <input type="email" name="email" class="form-control" value="<?= esc($row['email']) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Postinumero</label>
          <input name="postinumero" class="form-control" pattern="^\d{5}$" value="<?= esc($row['postinumero']) ?>">
        </div>

        <div class="col-12 d-flex gap-2 mt-3">
          <button class="btn btn-primary">Tallenna muutokset</button>
          <a href="list.php" class="btn btn-outline-secondary">Takaisin</a>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>


