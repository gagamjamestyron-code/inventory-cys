<?php
require_once 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $restock_level = $_POST['restock_level'] ?? 5;

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO items (name, category, quantity, price, restock_level) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $quantity, $price, $restock_level]);

        $log = $pdo->prepare("INSERT INTO history (action, item_name, quantity_change) VALUES (?, ?, ?)");
        $log->execute(['Added', $name, "+$quantity"]);
    }
}
header("Location: my_items.php");
exit();
?>