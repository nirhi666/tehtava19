<?php
require __DIR__.'/db.php';

$username = 'admin';
$password = 'salasana123'; // vain testiä varten

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
  INSERT INTO users (username, password_hash, role)
  VALUES (?, ?, 'admin')
  ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role='admin'
");
$stmt->bind_param('ss', $username, $hash);
$stmt->execute();

echo "OK: admin / salasana123";


