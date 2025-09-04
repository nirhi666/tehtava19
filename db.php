<?php
// --- KOTI (localhost + portti 3307) ---

$servername = "127.0.0.1";
$port = 3307;
$username = "root";
$password = "";
$dbname = "tievi23_marko";


// --- KOULU (palvelin, oletus 3306) ---
/*
$servername = "localhost";
$port = 3306;
$username = "tievi23_marko";
$password = "tievi23_mysql";
$dbname = "tievi23_marko";
*/

// --- Yhteys ---
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Yhteys epäonnistui: " . $conn->connect_error);
} else {
    // Testauksen ajaksi voit pitää tämän
    // echo "OK: yhteys toimii (server_info " . $conn->server_info . ")";
}
?>




