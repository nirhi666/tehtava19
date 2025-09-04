<?php
session_start();
require __DIR__.'/auth.php';
require_login();

$u = current_user();
require __DIR__.'/db.php';

function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
$isAdmin = (($u['role'] ?? 'user') === 'admin');

// Flashit
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_errors  = $_SESSION['flash_errors']  ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_errors']);

// Admin näkee käyttäjärekisterin
if ($isAdmin) {
    $sql = "SELECT id, username, role, ika, email, postinumero, created_at 
            FROM users ORDER BY id DESC";
    $result = $conn->query($sql);
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
} else {
    $rows = [];
}
?>

<?php if (is_file(__DIR__.'/header.php')) include __DIR__.'/header.php'; ?>
<div class="container py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0 text-primary">Käyttäjärekisteri</h2>
  </div>

  <?php if ($flash_success): ?><div class="alert alert-success"><?= esc($flash_success) ?></div><?php endif; ?>
  <?php if (!empty($flash_errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach($flash_errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body p-4">
      <?php if ($isAdmin): ?>
        <?php if (!empty($rows)): ?>
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Käyttäjänimi</th>
                  <th>Rooli</th>
                  <th>Ikä</th>
                  <th>Email</th>
                  <th>Postinumero</th>
                  <th>Luotu</th>
                  <th>Toiminnot</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= (int)$row['id'] ?></td>
                  <td><?= esc($row['username']) ?></td>
                  <td><?= esc($row['role']) ?></td>
                  <td><?= esc($row['ika']) ?></td>
                  <td><?= esc($row['email']) ?></td>
                  <td><?= esc($row['postinumero']) ?></td>
                  <td><?= esc($row['created_at']) ?></td>
                  <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-warning" href="edit_user.php?id=<?= (int)$row['id'] ?>">Muokkaa</a>
                    <a class="btn btn-sm btn-danger"
                       href="delete_user.php?id=<?= (int)$row['id'] ?>"
                       onclick="return confirm('Poistetaanko käyttäjä?')">Poista</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info mb-0">Ei käyttäjiä.</div>
        <?php endif; ?>
      <?php else: ?>
        <div class="alert alert-info">Vain admin voi tarkastella käyttäjärekisteriä.</div>
      <?php endif; ?>

      <a href="work_list.php" class="btn btn-success mt-3">Työvuorot (Aloita/Lopeta)</a>
    </div>
  </div>
</div>
<?php if (is_file(__DIR__.'/footer.php')) include __DIR__.'/footer.php'; ?>


