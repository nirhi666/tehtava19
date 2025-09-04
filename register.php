<?php
session_start();
require __DIR__.'/auth.php';

// Jos kirjautunut → listaan
if (is_logged_in()) {
    header("Location: list.php");
    exit;
}

// Jos ei kirjautunut → login-sivulle (josta löytyy myös linkki registeriin)
header("Location: login.php");
exit;


