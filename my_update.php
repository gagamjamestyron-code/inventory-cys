<?php
require_once 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $restock_level = $_POST['restock_level'] ?? 5;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE items SET name = ?, category = ?, quantity = ?, price = ?, restock_level = ? WHERE id = ?");
        $stmt->execute([$name, $category, $quantity, $price, $restock_level, $id]);

        $log = $pdo->prepare("INSERT INTO history (action, item_name, quantity_change) VALUES (?, ?, ?)");
        $log->execute(['Updated', $name, "$quantity"]);
    }
}
header("Location: my_items.php");
exit();
?>