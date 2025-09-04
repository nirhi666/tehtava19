<?php
require __DIR__.'/auth.php';
require_login();
$u = current_user();

require __DIR__.'/db.php';
function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

if ($u['role'] === 'admin') {
    $sql = "SELECT t.*, u.username 
            FROM tyoaika t 
            JOIN users u ON u.id = t.user_id 
            ORDER BY t.id DESC";
    $res = $conn->query($sql);
} else {
    $stmt = $conn->prepare("SELECT * FROM tyoaika WHERE user_id=? ORDER BY id DESC");
    $stmt->bind_param("i", $u['id']);
    $stmt->execute();
    $res = $stmt->get_result();
}
$rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Työvuorot</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-3 text-primary">Työvuorot</h2>
  <div class="mb-3 d-flex gap-2">
    <a href="work_start.php" class="btn btn-success">Aloita työ</a>
    <a href="work_end.php" class="btn btn-danger">Lopeta työ</a>
    <a href="list.php" class="btn btn-outline-primary">Takaisin rekisteriin</a>
  </div>

  <div class="card">
    <div class="card-body p-4">
      <table class="table table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <?php if ($u['role']==='admin'): ?><th>Käyttäjä</th><?php endif; ?>
            <th>Aloitus</th>
            <th>Lopetus</th>
            <th>Työvaiheet</th> <!-- UUSI -->
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <?php if ($u['role']==='admin'): ?><td><?= esc($r['username']) ?></td><?php endif; ?>
            <td><?= esc($r['aloitus']) ?></td>
            <td><?= esc($r['lopetus'] ?? '-') ?></td>
            <td>
              <a class="btn btn-sm btn-info"
                 href="work_phase_list.php?shift_id=<?= (int)$r['id'] ?>">
                 Avaa
              </a>
            </td> <!-- UUSI -->
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>


