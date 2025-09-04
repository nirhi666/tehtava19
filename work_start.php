<?php
require __DIR__.'/auth.php';
require_login();
$u = current_user();

require __DIR__.'/db.php';

// Onko jo avoin vuoro?
$stmt = $conn->prepare("SELECT id FROM tyoaika WHERE user_id=? AND lopetus IS NULL");
$stmt->bind_param("i", $u['id']);
$stmt->execute();
$open = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($open) {
  $_SESSION['flash_errors'] = ["Työvuoro on jo käynnissä."];
  header("Location: work_list.php"); exit;
}

// Aloita uusi vuoro nyt
$stmt = $conn->prepare("INSERT INTO tyoaika (user_id, aloitus) VALUES (?, NOW())");
$stmt->bind_param("i", $u['id']);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Työvuoro aloitettu!";
header("Location: work_list.php"); exit;

