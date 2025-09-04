<?php
require __DIR__.'/db.php';

$newUsername = 'admin';
$newPassword = 'Salasana123';
$newRole     = 'admin';

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

// Jos admin löytyy -> päivitä, muuten lisää
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $newUsername);
$stmt->execute();
$res = $stmt->get_result();
$u   = $res->fetch_assoc();
$stmt->close();

if ($u) {
    // Päivitä salasana ja rooli
    $stmt = $conn->prepare("UPDATE users SET password_hash=?, role=? WHERE username=?");
    $stmt->bind_param("sss", $hash, $newRole, $newUsername);
    $stmt->execute();
    $stmt->close();
    echo "Adminin salasana resetoitu. Käyttäjä: {$newUsername}, salasana: {$newPassword}";
} else {
    // Lisää uusi
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $newUsername, $hash, $newRole);
    $stmt->execute();
    $stmt->close();
    echo "Uusi admin luotu. Käyttäjä: {$newUsername}, salasana: {$newPassword}";
}



