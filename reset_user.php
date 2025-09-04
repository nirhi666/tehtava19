<?php
require __DIR__.'/auth.php';
require_admin();
require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Virheellinen ID");

$newPassword = "Salasana123"; // oletussalasana
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
$stmt->bind_param("si", $hash, $id);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Salasana resetoitu käyttäjälle (uusi: $newPassword)";
header("Location: users.php"); exit;


