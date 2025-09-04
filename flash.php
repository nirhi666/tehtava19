<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function flash($type, $msg) { $_SESSION['flash'][] = ['t'=>$type,'m'=>$msg]; }
function flash_out() {
  if (empty($_SESSION['flash'])) return;
  foreach ($_SESSION['flash'] as $f) {
    $t = htmlspecialchars($f['t']); $m = htmlspecialchars($f['m']);
    echo "<div class='alert alert-$t alert-dismissible fade show' role='alert'>
            $m
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
  }
  unset($_SESSION['flash']);
}



