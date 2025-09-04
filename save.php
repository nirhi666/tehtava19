<?php
// save.php – tallentaa lomakkeen ja liittää rivin kirjautuneeseen käyttäjään
session_start();
require __DIR__.'/auth.php';
require_login();
require __DIR__.'/db.php';

function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Poimi ja validoi
$nimi        = trim($_POST['nimi'] ?? '');
$ika         = isset($_POST['ika']) ? (int)$_POST['ika'] : null;
$email       = trim($_POST['email'] ?? '');
$osoite      = trim($_POST['osoite'] ?? '');
$postinumero = trim($_POST['postinumero'] ?? '');
$userId      = $_SESSION['user']['id'] ?? null;

$errors = [];
if ($nimi === '' || mb_strlen($nimi) < 3 || mb_strlen($nimi) > 150) $errors[] = 'Nimen tulee olla 3–150 merkkiä.';
if (!is_int($ika) || $ika < 0 || $ika > 118)                         $errors[] = 'Ikä pitää olla 0–118.';
if ($email === '' || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/u', $email)) $errors[] = 'Anna kelvollinen sähköpostiosoite.';
if ($osoite === '' || mb_strlen($osoite) > 255)                       $errors[] = 'Osoite on pakollinen.';
if (!preg_match('/^\d{5}$/', $postinumero))                           $errors[] = 'Postinumeron tulee olla 5 numeroa.';
if (!$userId)                                                         $errors[] = 'Käyttäjätunnus puuttuu (kirjaudu uudelleen).';

if ($errors) {
    $_SESSION['flash_errors'] = $errors;
    $_SESSION['old'] = [
        'nimi' => $nimi,
        'ika'  => $ika,
        'email' => $email,
        'osoite' => $osoite,
        'postinumero' => $postinumero,
    ];
    header('Location: index.php');
    exit;
}

// Tallenna
$stmt = $conn->prepare("
  INSERT INTO rekisteroinnit (nimi, ika, email, osoite, postinumero, user_id)
  VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('sisssi', $nimi, $ika, $email, $osoite, $postinumero, $userId);
$stmt->execute();

$_SESSION['flash_success'] = 'Rivi tallennettu.';
header('Location: list.php');
exit;



