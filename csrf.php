<?php
// csrf.php
if (session_status() === PHP_SESSION_NONE) session_start();

function csrf_token(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}
function csrf_field(): string {
  $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
  return '<input type="hidden" name="csrf" value="'.$t.'">';
}
function csrf_verify_post(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die('Method not allowed');
  }
  $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
  if (!$ok) { http_response_code(403); die('CSRF check failed'); }
}



