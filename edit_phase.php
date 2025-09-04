<?php
require __DIR__.'/auth.php';
require_login();
require_admin(); // vain admin voi muokata työvaiheita
$u = current_user();

require __DIR__.'/db.php';
function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$id = (int)($_GET['id'] ?? 0);
$shift_id = (int)($_GET['shift_id'] ?? 0);
if ($id <= 0 || $shift_id <= 0) {
    http_response_code(400);
    die("Virheellinen ID");
}

// Hae työvaihe
$stmt = $conn->prepare("SELECT * FROM tyovaiheet WHERE id=? AND tyoaika_id=?");
$stmt->bind_param("ii", $id, $shift_id);
$stmt->execute();
$res = $stmt->get_result();
$phase = $res->fetch_assoc();
$stmt->close();

if (!$phase) {
    http_response_code(404);
    die("Työvaihetta ei löytynyt.");
}

// Jos lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kuvaus = trim($_POST['kuvaus'] ?? '');
    if ($kuvaus === '') {
        $_SESSION['flash_errors'] = ["Kuvaus ei voi olla tyhjä."];
    } else {
        $stmt = $conn->prepare("UPDATE tyovaiheet SET kuvaus = CONCAT(?, ' (muokattu admin)') WHERE id=?");
        $stmt->bind_param("si", $kuvaus, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash_success'] = "Työvaihe päivitetty.";
        header("Location: work_phase_list.php?shift_id=".$shift_id);
        exit;
    }
}
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Muokkaa työvaihetta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:700px">
  <h2 class="mb-3 text-primary">Muokkaa työvaihetta</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Kuvaus</label>
      <input type="text" name="kuvaus" class="form-control" value="<?= esc($phase['kuvaus']) ?>" required>
    </div>
    <button class="btn btn-primary">Tallenna</button>
    <a href="work_phase_list.php?shift_id=<?= (int)$shift_id ?>" class="btn btn-secondary">Peruuta</a>
  </form>
</div>
</body>
</html>



