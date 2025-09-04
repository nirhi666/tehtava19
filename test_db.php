<?php
require __DIR__.'/db.php'; // käyttää koulun asetuksia

// Näytä users-taulun rivit ja hashin pituus
$r = $conn->query("SELECT id, username, LENGTH(password_hash) AS len, role, created_at FROM users");
echo "<pre>";
print_r($r->fetch_all(MYSQLI_ASSOC));


