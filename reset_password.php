<?php
require __DIR__.'/db.php';
$username = 'admin';       // <-- vaihda tähän
$newPass  = 'Salasana123'; // <-- vaihda tähän
$hash = password_hash($newPass, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE username=?");
$stmt->bind_param("ss", $hash, $username);
$stmt->execute();
echo $stmt->affected_rows ? "OK. Uusi salasana: $newPass" : "Ei päivitetty.";


