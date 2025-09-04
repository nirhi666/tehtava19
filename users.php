<?php
require __DIR__.'/auth.php';
require_admin(); // vain admin pääsee
require __DIR__.'/db.php';

function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$rows = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$u = current_user();
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Käyttäjät (admin)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0 text-primary">
      Käyttäjät (admin) <span class="badge bg-secondary ms-2"><?= count($rows) ?></span>
    </h2>
    <div class="small text-muted">
      Kirjautunut: <strong><?= esc($u['username']) ?></strong> (<?= esc($u['role']) ?>)
      &nbsp;|&nbsp; <a href="logout.php">Uloskirjautuminen</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-4">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th><th>Käyttäjänimi</th><th>Rooli</th><th>Luotu</th><th>Toiminnot</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= esc($r['username']) ?></td>
                <td>
                  <?php if ($r['role']==='admin'): ?>
                    <span class="badge bg-primary">admin</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">user</span>
                  <?php endif; ?>
                </td>
                <td><?= esc($r['created_at']) ?></td>
                <td class="d-flex gap-2">
                  <a href="edit_user.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-warning">Muokkaa</a>
                  <a href="reset_user.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-info"
                     onclick="return confirm('Resetoi salasana?')">Reset</a>
                  <a href="delete_user.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Poistetaanko tämä käyttäjä?')">Poista</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <a class="btn btn-outline-primary" href="list.php">Takaisin listoihin</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
