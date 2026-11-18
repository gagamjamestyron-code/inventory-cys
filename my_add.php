<?php
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - MyInventory</title>
    <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>
<div class="app-shell">
    <nav class="sidebar">
        <div>
            <div class="logo-area">🛒 MyInventory <span class="logo-sub">Track • Manage • Restock</span></div>
            <ul>
                <li><a href="my_dashboard.php">📊 Dashboard</a></li>
                <li><a href="javascript:void(0)" onclick="toggleSubmenu(event)">📦 Items ▾</a>
                    <ul class="submenu" id="itemSubmenu"><li><a href="my_items.php">View Items</a></li><li><a href="my_add.php" class="active">Add Item</a></li></ul>
                </li>
                <li><a href="my_history.php">🕒 History</a></li>
            </ul>
        </div>
        <div><ul><li><a href="settings.php">⚙️ Settings</a></li><li><a href="logout.php">🚪 Logout</a></li></ul></div>
    </nav>
    <main class="main-content">
        <div class="header-top"><div><h1>Add New Item</h1><p>Fill in the product details below.</p></div></div>
        <div class="form-card">
            <form action="my_save.php" method="POST">
                <div class="form-group"><label for="name">Item Name</label><input id="name" type="text" name="name" placeholder="e.g., Rice, Milk, Eggs" required></div>
                <div class="form-group"><label for="category">Category</label><input id="category" type="text" name="category" placeholder="e.g., Pantry, Beverages, Snacks" required></div>
                <div class="form-group"><label for="quantity">Quantity</label><input id="quantity" type="text" name="quantity" placeholder="e.g., 5 kg, 2 cartons" required></div>
                <div class="form-group"><label for="price">Price (₱)</label><input id="price" type="number" step="0.01" min="0" name="price" placeholder="0.00" required></div>
                <div class="form-group"><label for="restock_level">Restock Level Threshold</label><input id="restock_level" type="number" min="0" name="restock_level" placeholder="e.g., 2" required></div>
                <div class="form-actions"><button type="submit" class="btn-action">Save Item</button><a href="my_items.php" class="btn-action delete">Cancel</a></div>
            </form>
        </div>
    </main>
</div>
<script>
function toggleSubmenu(e) { e.preventDefault(); const menu = document.getElementById('itemSubmenu'); menu.style.display = menu.style.display === 'block' ? 'none' : 'block'; }
</script>
</body>
</html>
