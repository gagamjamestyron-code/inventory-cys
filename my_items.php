<?php
require_once 'includes/db.php';
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE name LIKE ? OR category LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
}
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyInventory - Items</title>
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
                        <li><a href="my_items.php" class="active">View Items</a></li>
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
                <h1>Items Management</h1>
                <p>Manage, search, edit, and delete inventory stocks.</p>
            </div>
            <a href="my_add.php" class="btn-action" style="padding: 10px 18px; text-decoration:none;">+ Add New Item</a>
        </div>

        <div class="section-box">
            <form method="GET" style="margin-bottom: 20px; display:flex; gap:10px;">
                <input type="text" name="search" placeholder="Search items or categories..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; width: 300px; background:var(--bg-dark); border:1px solid var(--border-color); color:#fff; border-radius:8px;">
                <button type="submit" class="btn-action">Search</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Date Added</th>
                        <th>Restock Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($items) > 0): foreach ($items as $item): 
                        $qtyVal = intval($item['quantity']);
                        $restockVal = intval($item['restock_level']);
                        
                        if ($qtyVal <= $restockVal) {
                            $statusClass = "low"; 
                            $statusText = "Low Stock";
                        } else {
                            $statusClass = "normal"; 
                            $statusText = "Normal";
                        }
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td><span class="pill"><?php echo htmlspecialchars($item['category']); ?></span></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($item['date_added'])); ?></td>
                        <td><?php echo $item['restock_level']; ?></td>
                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                        <td>
                            <a href="my_edit.php?id=<?php echo $item['id']; ?>" class="btn-action">Edit</a>
                            <a href="my_delete.php?id=<?php echo $item['id']; ?>" class="btn-action delete" onclick="return confirm('Delete this item?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8" style="text-align:center; color:var(--text-muted);">No items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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