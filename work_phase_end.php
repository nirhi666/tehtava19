<?php
require __DIR__.'/auth.php';
require_login();
$u = current_user();
require __DIR__.'/db.php';

$shift_id = (int)($_POST['shift_id'] ?? 0);
$phase_id = (int)($_POST['phase_id'] ?? 0);
if ($shift_id <= 0 || $phase_id <= 0) {
  $_SESSION['flash_errors'] = ["Virheellinen pyyntö."];
  header("Location: work_list.php"); exit;
}

$isAdmin = (($u['role'] ?? '') === 'admin');

// varmista oikeus vuoroon
if ($isAdmin) {
  $stmt = $conn->prepare("SELECT id FROM tyoaika WHERE id=?");
  $stmt->bind_param("i", $shift_id);
} else {
  $stmt = $conn->prepare("SELECT id FROM tyoaika WHERE id=? AND user_id=?");
  $stmt->bind_param("ii", $shift_id, $u['id']);
}
$stmt->execute();
$ok = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$ok) { http_response_code(403); die("Ei oikeutta vuoroon."); }

// lopeta vain jos avoin
$stmt = $conn->prepare("UPDATE tyovaiheet SET lopetus=NOW()
                        WHERE id=? AND tyoaika_id=? AND lopetus IS NULL");
$stmt->bind_param("ii", $phase_id, $shift_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  $_SESSION['flash_success'] = "Työvaihe lopetettu.";
} else {
  $_SESSION['flash_errors'] = ["Avoinna olevaa työvaihetta ei löytynyt."];
}
$stmt->close();

header("Location: work_phase_list.php?shift_id=".$shift_id); exit;


