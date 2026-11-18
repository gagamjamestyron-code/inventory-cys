<?php
require_once 'includes/db.php';
$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();
if (!$item) { http_response_code(404); exit('Item not found.'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - MyInventory</title>
    <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>
<div class="app-shell">
    <nav class="sidebar">
        <div><div class="logo-area">🛒 MyInventory <span class="logo-sub">Track • Manage • Restock</span></div><ul>
            <li><a href="my_dashboard.php">📊 Dashboard</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSubmenu(event)">📦 Items ▾</a><ul class="submenu" id="itemSubmenu"><li><a href="my_items.php">View Items</a></li><li><a href="my_add.php">Add Item</a></li></ul></li>
            <li><a href="my_history.php">🕒 History</a></li>
        </ul></div>
        <div><ul><li><a href="settings.php">⚙️ Settings</a></li><li><a href="logout.php">🚪 Logout</a></li></ul></div>
    </nav>
    <main class="main-content">
        <div class="header-top"><div><h1>Edit Item</h1><p>Modify the details of your inventory item.</p></div></div>
        <div class="form-card"><form action="my_update.php" method="POST">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <div class="form-group"><label for="name">Item Name</label><input id="name" type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required></div>
            <div class="form-group"><label for="category">Category</label><input id="category" type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>" required></div>
            <div class="form-group"><label for="quantity">Quantity</label><input id="quantity" type="text" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required></div>
            <div class="form-group"><label for="price">Price (₱)</label><input id="price" type="number" min="0" step="0.01" name="price" value="<?= htmlspecialchars((string)$item['price']) ?>" required></div>
            <div class="form-group"><label for="restock_level">Restock Level Threshold</label><input id="restock_level" type="number" min="0" name="restock_level" value="<?= (int)$item['restock_level'] ?>" required></div>
            <div class="form-actions"><button type="submit" class="btn-action">Update Item</button><a href="my_items.php" class="btn-action delete">Cancel</a></div>
        </form></div>
    </main>
</div>
<script>function toggleSubmenu(e){e.preventDefault();const s=document.getElementById('itemSubmenu');s.style.display=s.style.display==='block'?'none':'block';}</script>
</body>
</html>
