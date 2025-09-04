<?php
require __DIR__.'/auth.php';
require_login();
require_admin(); // vain admin saa päivittää
$u = current_user();

require __DIR__.'/db.php';

function back($msg){
  $_SESSION['flash_errors'] = [$msg];
  header("Location: list.php"); exit;
}

$id          = (int)($_POST['id'] ?? 0);
$username    = trim($_POST['username'] ?? '');
$ika_raw     = trim($_POST['ika'] ?? '');
$email       = trim($_POST['email'] ?? '');
$postinumero = trim($_POST['postinumero'] ?? '');
$role        = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

if ($id <= 0) back("Virheellinen ID.");
if ($username==='' || $ika_raw==='' || $email==='' || $postinumero==='') back("Täytä kaikki kentät.");

// Validoinnit
if (mb_strlen($username)<3 || mb_strlen($username)>60) back("Käyttäjänimen pituus 3–60.");
$ika = filter_var($ika_raw, FILTER_VALIDATE_INT, ['options'=>['min_range'=>0,'max_range'=>118]]);
if ($ika === false) back("Ikä virheellinen (0–118).");
if (mb_strlen($email)>150 || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/u',$email)) back("Sähköposti virheellinen.");
if (!preg_match('/^\d{5}$/',$postinumero)) back("Postinumero 5 numeroa.");

// Päivitys users-tauluun
$stmt = $conn->prepare("
  UPDATE users 
  SET username=?, role=?, ika=?, email=?, postinumero=?
  WHERE id=?
");
$stmt->bind_param("ssissi", $username, $role, $ika, $email, $postinumero, $id);

$ok = $stmt->execute();
if ($ok) {
  $_SESSION['flash_success'] = "Muutokset tallennettu.";
} else {
  $_SESSION['flash_errors'] = ["Tallennus epäonnistui: ".$stmt->error];
}
$stmt->close();

header("Location: list.php"); exit;


