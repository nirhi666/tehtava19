<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user'] = ['id'=>999, 'username'=>'testi', 'role'=>'admin'];
session_write_close(); // varmistaa levykirjoituksen ennen redirectiä
header('Location: get_session_test.php');
exit;


