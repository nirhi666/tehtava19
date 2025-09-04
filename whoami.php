<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');

echo "SESSION USER:\n";
var_export($_SESSION['user'] ?? null);
echo "\n";



