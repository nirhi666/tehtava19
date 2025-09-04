<?php
require __DIR__.'/auth.php';
require_login();
require_admin();              // <-- VAIN ADMIN SAA POISTAA
$u = current_user();

require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { 
    header("Location: list.php"); 
    exit; 
}

$stmt = $conn->prepare("DELETE FROM rekisteroinnit WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  $_SESSION['flash_success'] = "Rivi poistettu.";
} else {
  $_SESSION['flash_errors'] = ["Riviä ei löytynyt tai poisto epäonnistui."];
}
$stmt->close();

header("Location: list.php"); exit;




