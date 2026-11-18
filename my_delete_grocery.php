<?php
require_once 'includes/db.php';
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM grocery_list WHERE id = :id');
    $stmt->execute([':id' => $id]);
}
header('Location: my_dashboard.php');
exit;
