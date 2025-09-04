<?php
require __DIR__.'/auth.php';
require_login();
$u = current_user();
require __DIR__.'/db.php';

$shift_id = (int)($_POST['shift_id'] ?? 0);
$kuvaus   = trim($_POST['kuvaus'] ?? '');

if ($shift_id <= 0 || $kuvaus === '') {
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

// onko jo avoin vaihe
$stmt = $conn->prepare("SELECT id FROM tyovaiheet WHERE tyoaika_id=? AND lopetus IS NULL");
$stmt->bind_param("i", $shift_id);
$stmt->execute();
$open = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($open) {
  $_SESSION['flash_errors'] = ["Työvaihe on jo käynnissä."];
  header("Location: work_phase_list.php?shift_id=".$shift_id); exit;
}

// aloita
$stmt = $conn->prepare("INSERT INTO tyovaiheet (tyoaika_id, kuvaus, aloitus) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $shift_id, $kuvaus);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Työvaihe aloitettu.";
header("Location: work_phase_list.php?shift_id=".$shift_id); exit;


