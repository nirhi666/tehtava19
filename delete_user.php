<?php
require __DIR__.'/auth.php';
require_admin();
require __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Virheellinen ID");

// Estetään että admin ei poista itseään vahingossa
if ($id === (int)($_SESSION['user']['id'])) {
    $_SESSION['flash_errors'] = ["Et voi poistaa itseäsi!"];
    header("Location: users.php"); exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Käyttäjä poistettu.";
header("Location: users.php"); exit;



