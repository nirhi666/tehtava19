<?php
// repair.php — AJA SELAIMELLA KERRAN, POISTA SITTEN TIEDOSTO
require __DIR__.'/db.php';

header('Content-Type: text/plain; charset=utf-8');

function q($conn, $sql){
  if (!$conn->query($sql)) {
    die("SQL-virhe: ".$conn->error."\nKysely: ".$sql."\n");
  }
}

// 1) Users-taulu kuntoon
q($conn, "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "OK: users-taulu on olemassa.\n";

// 2) Tee admin-käyttäjä (tai päivitä se). Salasana: Admin12345
$adminUser = 'admin';
$adminPass = 'Admin12345';
$hash = password_hash($adminPass, PASSWORD_DEFAULT);

// yritä päivittää; jos ei ole, lisää
$stmt = $conn->prepare("UPDATE users SET password_hash=?, role='admin' WHERE username=?");
$stmt->bind_param("ss", $hash, $adminUser);
$stmt->execute();

if ($stmt->affected_rows === 0) {
  $stmt2 = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
  $stmt2->bind_param("ss", $adminUser, $hash);
  $stmt2->execute();
  $stmt2->close();
  echo "OK: admin luotu käyttäjätunnuksella 'admin'.\n";
} else {
  echo "OK: admin päivitetty.\n";
}
$stmt->close();

// 3) (Valinnainen) Lisää rekisteroinnit.user_id jos puuttuu
$res = $conn->query("SHOW COLUMNS FROM rekisteroinnit LIKE 'user_id'");
if ($res && $res->num_rows === 0) {
  q($conn, "ALTER TABLE rekisteroinnit ADD COLUMN user_id INT NULL");
  // vierasavain (jos users on samaa kantaa)
  // hiljennetään virheet jos FK oli jo olemassa
  @$conn->query("ALTER TABLE rekisteroinnit
                 ADD CONSTRAINT fk_rek_user
                 FOREIGN KEY (user_id) REFERENCES users(id)
                 ON DELETE SET NULL ON UPDATE CASCADE");
  echo "OK: lisätty rekisteroinnit.user_id (+FK jos mahdollista).\n";
} else {
  echo "OK: rekisteroinnit.user_id on jo olemassa.\n";
}

echo "\nVALMIS.\nKäyttäjä: admin  |  Salasana: {$adminPass}\n";



