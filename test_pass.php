<?php
$salasana = "admin123"; // tähän se salasana mitä LUULET olevan
$hash = '$2y$10$e0NRvYwH5lEwOEK.I7tMeugfU5aj/nWxGSSS4kVvbeYt8KRBqCe'; // tähän se mikä on tietokannassa

if (password_verify($salasana, $hash)) {
    echo "✅ Täsmää!";
} else {
    echo "❌ Ei täsmää!";
}


