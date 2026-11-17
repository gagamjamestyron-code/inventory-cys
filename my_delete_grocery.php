<?php
require_once 'includes/db.php';
$id = $_GET['id'] ?? 0;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM grocery_list WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: my_dashboard.php");
exit();
?>