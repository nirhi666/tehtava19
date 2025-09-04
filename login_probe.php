<?php
// login_probe.php — TARKISTAA SUORAAN kannasta valitun käyttäjän ja password_verify-tuloksen
require __DIR__.'/db.php';

$user = 'admin';          // vaihda jos testaat toista
$pass = 'Salasana123';    // sama kuin reset_admin.php:ssä

$stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username=?");
if (!$stmt) die('Prepare error: '.$conn->error);
$stmt->bind_param("s", $user);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

echo "<pre>";
if (!$row) {
  echo "Ei löytynyt käyttäjää: $user\n";
  exit;
}
echo "Käyttäjä löytyi:\n";
print_r($row);

$ok = password_verify($pass, $row['password_hash']);
echo "\npassword_verify: ".($ok ? "OK" : "FAIL")."\n";



