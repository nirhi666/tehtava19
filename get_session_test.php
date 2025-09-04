<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: text/plain; charset=utf-8');
echo "SESSION USER:\n";
var_dump($_SESSION['user'] ?? null);



