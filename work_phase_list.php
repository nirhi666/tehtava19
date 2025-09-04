<?php
require __DIR__.'/auth.php';
require_login();
$u = current_user();
require __DIR__.'/db.php';
function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$shift_id = (int)($_GET['shift_id'] ?? 0);
if ($shift_id <= 0) { http_response_code(400); die("Virheellinen vuoro-ID"); }

$isAdmin = (($u['role'] ?? '') === 'admin');

// Varmista oikeus vuoroon
if ($isAdmin) {
  $stmt = $conn->prepare("SELECT t.*, u.username FROM tyoaika t JOIN users u ON u.id=t.user_id WHERE t.id=?");
  $stmt->bind_param("i", $shift_id);
} else {
  $stmt = $conn->prepare("SELECT t.*, u.username FROM tyoaika t JOIN users u ON u.id=t.user_id WHERE t.id=? AND t.user_id=?");
  $stmt->bind_param("ii", $shift_id, $u['id']);
}
$stmt->execute();
$shift = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$shift) { http_response_code(403); die("Ei oikeutta tähän vuoroon."); }

// Hae työvaiheet
$stmt = $conn->prepare("SELECT * FROM tyovaiheet WHERE tyoaika_id=? ORDER BY id DESC");
$stmt->bind_param("i", $shift_id);
$stmt->execute();
$res = $stmt->get_result();
$phases = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();

// Avoin vaihe?
$openPhase = null;
foreach ($phases as $p) { if ($p['lopetus'] === null) { $openPhase = $p; break; } }

include __DIR__.'/header.php';
?>
<h2 class="mb-3 text-primary">Työvaiheet – vuoro #<?= (int)$shift['id'] ?> (<?= esc($shift['username']) ?>)</h2>

<div class="mb-3">
  <a href="work_list.php" class="btn btn-outline-secondary">Takaisin työvuoroihin</a>
</div>

<div class="card mb-4">
  <div class="card-body">
    <div><strong>Aloitus:</strong> <?= esc($shift['aloitus']) ?></div>
    <div><strong>Lopetus:</strong> <?= esc($shift['lopetus'] ?? 'Käynnissä') ?></div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <?php if (!$openPhase): ?>
      <form class="row g-2" action="work_phase_start.php" method="post">
        <input type="hidden" name="shift_id" value="<?= (int)$shift_id ?>">
        <div class="col-md-8">
          <input name="kuvaus" class="form-control" required maxlength="255" placeholder="Työvaiheen kuvaus (esim. lastaus)">
        </div>
        <div class="col-md-4 d-grid">
          <button class="btn btn-success">Aloita työvaihe</button>
        </div>
      </form>
    <?php else: ?>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="text-warning">
          Avoin työvaihe: <strong>#<?= (int)$openPhase['id'] ?></strong> – <?= esc($openPhase['kuvaus']) ?>
          (alkoi <?= esc($openPhase['aloitus']) ?>)
        </div>
        <form action="work_phase_end.php" method="post" class="ms-auto">
          <input type="hidden" name="shift_id" value="<?= (int)$shift_id ?>">
          <input type="hidden" name="phase_id" value="<?= (int)$openPhase['id'] ?>">
          <button class="btn btn-danger">Lopeta työvaihe</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Kuvaus</th>
          <th>Aloitus</th>
          <th>Lopetus</th>
          <?php if ($isAdmin): ?><th>Toiminnot</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if ($phases): foreach ($phases as $p): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= esc($p['kuvaus']) ?></td>
            <td><?= esc($p['aloitus']) ?></td>
            <td><?= esc($p['lopetus'] ?? '–') ?></td>
            <?php if ($isAdmin): ?>
              <td class="d-flex gap-2">
                <a class="btn btn-sm btn-warning"
                   href="edit_phase.php?id=<?= (int)$p['id'] ?>&shift_id=<?= (int)$shift_id ?>">
                   Muokkaa
                </a>
                <?php if ($p['lopetus'] === null): ?>
                  <form method="post" action="work_phase_end.php" class="d-inline">
                    <input type="hidden" name="shift_id" value="<?= (int)$shift_id ?>">
                    <input type="hidden" name="phase_id" value="<?= (int)$p['id'] ?>">
                    <button class="btn btn-sm btn-danger"
                            onclick="return confirm('Lopetetaanko tämä työvaihe?')">
                      Lopeta
                    </button>
                  </form>
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="<?= $isAdmin ? 5 : 4 ?>" class="text-center text-muted py-4">Ei työvaiheita.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__.'/footer.php'; ?>



