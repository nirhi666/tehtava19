<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/auth.php';
$u = current_user();
?>
<!doctype html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title><?= $title ?? 'CRUD-sovellus' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f6f7fb; }
    .page-wrap{ max-width:1100px; }
    .card{ border:0; box-shadow:0 10px 25px rgba(0,0,0,.06); border-radius:1rem; }
    .navbar-brand{ font-weight:700; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <!-- Brändilogo: vieraat -> index, kirjautuneet -> list -->
    <a class="navbar-brand" href="<?= $u ? 'list.php' : 'index.php' ?>">JEDU CRUD</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <?php if (!$u): ?>
          <!-- Vieras: rekisteröinti ja login -->
          <li class="nav-item"><a class="nav-link" href="index.php">Rekisteröidy</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Kirjaudu</a></li>
        <?php else: ?>
          <!-- Kirjautunut -->
          <li class="nav-item"><a class="nav-link" href="list.php">Listaus</a></li>
          <li class="nav-item"><a class="nav-link" href="work_list.php">Työvuorot</a></li>
          <?php if (($u['role'] ?? '') === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="users.php">Käyttäjät</a></li>
            <li class="nav-item"><a class="nav-link" href="list.php">Rekisteri</a></li>
          <?php endif; ?>
        <?php endif; ?>

      </ul>
      
      <?php if ($u): ?>
        <span class="navbar-text me-3">
          Kirjautunut: <strong><?= htmlspecialchars($u['username']) ?></strong> (<?= htmlspecialchars($u['role']) ?>)
        </span>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Kirjaudu ulos</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container py-4 page-wrap">
