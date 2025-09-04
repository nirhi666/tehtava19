<?php
// reset_admin.php — ASETTAA UUDEN SALASANAN ADMINILLE
require __DIR__.'/db.php';

$newPassword = 'Salasana123'; // voit muuttaa
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

if (!$hash) { die('password_hash epäonnistui'); }

$stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE username='admin'");
if (!$stmt) { die('Prepare epäonnistui: '.$conn->error); }

$stmt->bind_param("s", $hash);
if (!$stmt->execute()) { die('Execute epäonnistui: '.$stmt->error); }

echo "OK. Uusi admin-salasana on: ".$newPassword;



