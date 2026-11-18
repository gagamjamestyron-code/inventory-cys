<?php
require_once 'includes/db.php';
$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM history WHERE id = :id');
    $stmt->execute([':id' => $id]);
}
header('Location: my_history.php');
exit;
