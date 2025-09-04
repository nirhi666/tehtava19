<?php
require __DIR__.'/auth.php';
require_login();
$u = current_user();
require __DIR__.'/db.php';

$stmt = $conn->prepare("UPDATE tyoaika SET lopetus=NOW() WHERE user_id=? AND lopetus IS NULL");
$stmt->bind_param("i", $u['id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  $_SESSION['flash_success'] = "Työvuoro lopetettu.";
} else {
  $_SESSION['flash_errors'] = ["Ei avoinna olevaa työvuoroa!"];
}
$stmt->close();

header("Location: work_list.php"); exit;


