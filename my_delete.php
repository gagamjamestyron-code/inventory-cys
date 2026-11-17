<?php
require_once 'includes/db.php';
$id = $_GET['id'] ?? 0;
if ($id) {
    $getName = $pdo->prepare("SELECT name FROM items WHERE id = ?");
    $getName->execute([$id]);
    $item = $getName->fetch();

    if ($item) {
        $del = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $del->execute([$id]);

        $log = $pdo->prepare("INSERT INTO history (action, item_name, quantity_change) VALUES (?, ?, ?)");
        $log->execute(['Deleted', $item['name'], "—"]);
    }
}
header("Location: my_items.php");
exit();
?>