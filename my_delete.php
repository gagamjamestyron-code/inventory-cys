<?php
require_once 'includes/db.php';

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
if ($id > 0) {
    $getName = $pdo->prepare('SELECT name FROM items WHERE id = :id');
    $getName->execute([':id' => $id]);
    $item = $getName->fetch();

    if ($item) {
        $del = $pdo->prepare('DELETE FROM items WHERE id = :id');
        $del->execute([':id' => $id]);

        $log = $pdo->prepare('INSERT INTO history (action, item_name, quantity_change) VALUES (:action, :name, :change)');
        $log->execute([':action' => 'Deleted', ':name' => $item['name'], ':change' => '—']);
    }
}
header('Location: my_items.php');
exit;
