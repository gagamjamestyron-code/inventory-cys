<?php
require_once 'includes/db.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) { die("Item not found!"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item - MyInventory</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="logo-area">🛒 MyInventory <span class="logo-sub">Track • Manage • Restock</span></div>
            <ul>
                <li><a href="my_dashboard.php">📊 Dashboard</a></li>
                <li>
                    <a href="javascript:void(0);" onclick="toggleSubmenu(event)">📦 Items ▾</a>
                    <ul class="submenu" id="itemSubmenu">
                        <li><a href="my_items.php">View Items</a></li>
                        <li><a href="my_add.php">Add Item</a></li>
                    </ul>
                </li>
                <li><a href="my_history.php">🕒 History</a></li>
            </ul>
        </div>
        <div>
            <ul>
                <li><a href="settings.php">⚙️ Settings</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="header-top">
            <div>
                <h1>Edit Item</h1>
                <p>Modify the details of your inventory item.</p>
            </div>
        </div>

        <div class="form-card">
            <form action="my_update.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($item['category']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input type="text" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Price (₱)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $item['price']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Restock Level Threshold</label>
                    <input type="number" name="restock_level" value="<?php echo $item['restock_level']; ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-action" style="padding: 12px 24px; font-size:14px;">Update Item</button>
                    <a href="my_items.php" class="btn-action delete" style="padding: 12px 24px; font-size:14px; text-decoration:none; display:inline-flex; align-items:center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <script>
    function toggleSubmenu(e) {
        e.preventDefault();
        const submenu = document.getElementById('itemSubmenu');
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
    }
    </script>
</body>
</html>