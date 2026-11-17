<?php
require_once 'includes/db.php';

if (isset($_POST['add_grocery'])) {
    $g_name = trim($_POST['grocery_name']);
    if (!empty($g_name)) {
        $stmt = $pdo->prepare("INSERT INTO grocery_list (item_name) VALUES (?)");
        $stmt->execute([$g_name]);
    }
    header("Location: my_dashboard.php");
    exit();
}

if (isset($_GET['toggle_grocery'])) {
    $g_id = $_GET['toggle_grocery'];
    $pdo->query("UPDATE grocery_list SET is_checked = CASE WHEN is_checked = 1 THEN 0 ELSE 1 END WHERE id = $g_id");
    header("Location: my_dashboard.php");
    exit();
}

$totalItems = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$lowStockCount = $pdo->query("SELECT COUNT(*) FROM items WHERE CAST(quantity AS INTEGER) <= restock_level")->fetchColumn();
$categoriesCount = $pdo->query("SELECT COUNT(DISTINCT category) FROM items")->fetchColumn();

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

$query = "SELECT * FROM items WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category_filter) {
    $query .= " AND category = ?";
    $params[] = $category_filter;
}
$query .= " ORDER BY id DESC LIMIT 5";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

$history = $pdo->query("SELECT * FROM history ORDER BY timestamp DESC LIMIT 3")->fetchAll();
$groceries = $pdo->query("SELECT * FROM grocery_list ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyInventory - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="logo-area">
                🛒 MyInventory
                <span class="logo-sub">Track • Manage • Restock</span>
            </div>
            <ul>
                <li><a href="my_dashboard.php" class="active">📊 Dashboard</a></li>
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
                <h1>Dashboard 👋</h1>
                <p>Here's your inventory overview!</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">📦</div>
                <div class="stat-info">
                    <h3>Total Items</h3>
                    <div class="value"><?php echo $totalItems; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">⚠️</div>
                <div class="stat-info">
                    <h3>Low Stock</h3>
                    <div class="value" style="color:var(--warning);"><?php echo $lowStockCount; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">🛒</div>
                <div class="stat-info">
                    <h3>Categories</h3>
                    <div class="value" style="color:var(--success);"><?php echo $categoriesCount; ?></div>
                </div>
            </div>
            <div class="alert-banner" style="<?php echo ($lowStockCount == 0) ? 'background: linear-gradient(135deg, #065f46, #04382c); border-color: #059669;' : ''; ?>">
                <div>
                    <h4 style="<?php echo ($lowStockCount == 0) ? 'color: #34d399;' : ''; ?>">
                        <?php echo ($lowStockCount > 0) ? '⚠️ Low Stock Alert' : '✅ Stock Status Normal'; ?>
                    </h4>
                    <p style="<?php echo ($lowStockCount == 0) ? 'color: #a7f3d0;' : ''; ?>">
                        <?php echo ($lowStockCount > 0) ? "$lowStockCount items are running low!" : "All items have sufficient stock."; ?>
                    </p>
                </div>
                <a href="my_items.php" class="btn-alert" style="<?php echo ($lowStockCount == 0) ? 'border-color: #34d399; color: #34d399;' : ''; ?>">View Items →</a>
            </div>
        </div>

        <div class="section-box" style="padding: 15px 20px;">
            <form method="GET" style="display:flex; gap:15px; align-items:center;">
                <input type="text" name="search" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1; padding:10px; background:var(--bg-dark); border:1px solid var(--border-color); color:#fff; border-radius:8px;">
                <input type="text" name="category" placeholder="Filter category..." value="<?php echo htmlspecialchars($category_filter); ?>" style="padding:10px; background:var(--bg-dark); border:1px solid var(--border-color); color:#fff; border-radius:8px;">
                <button type="submit" class="btn-action" style="padding:10px 20px;">Filter</button>
            </form>
        </div>

        <div class="section-box">
            <div class="section-header">
                <h3>📦 Your Items</h3>
                <a href="my_items.php" class="btn-action">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($items) > 0): foreach ($items as $item): 
                        $qtyVal = intval($item['quantity']);
                        $restockVal = intval($item['restock_level']);
                        $statusClass = ($qtyVal <= $restockVal) ? "low" : "normal";
                        $statusText = ($qtyVal <= $restockVal) ? "Low Stock" : "Normal";
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td><span class="pill"><?php echo htmlspecialchars($item['category']); ?></span></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                        <td>
                            <a href="my_edit.php?id=<?php echo $item['id']; ?>" class="btn-action">Edit</a>
                            <a href="my_delete.php?id=<?php echo $item['id']; ?>" class="btn-action delete" onclick="return confirm('Delete item?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">No items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="bottom-grid">
            <div class="section-box" style="margin-bottom:0;">
                <div class="section-header">
                    <h3>🕒 Recent Activity</h3>
                    <a href="my_history.php" style="color:var(--primary); font-size:13px; text-decoration:none;">View All →</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Item</th>
                            <th>Change</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($history) > 0): foreach ($history as $h): ?>
                        <tr>
                            <td style="color:var(--primary); font-weight:600;"><?php echo htmlspecialchars($h['action']); ?></td>
                            <td><?php echo htmlspecialchars($h['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($h['quantity_change']); ?></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($h['timestamp'])); ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="4" style="text-align:center; color:var(--text-muted);">No recent activity.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-box" style="margin-bottom:0;">
                <div class="section-header">
                    <h3>🛒 Grocery List</h3>
                </div>
                <form method="POST" class="grocery-form">
                    <input type="text" name="grocery_name" placeholder="Add item..." required>
                    <button type="submit" name="add_grocery" class="btn-action">Add</button>
                </form>
                <ul style="list-style:none; padding:0; margin:0; max-height: 180px; overflow-y:auto;">
                    <?php if (count($groceries) > 0): foreach ($groceries as $g): ?>
                    <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color); display:flex; align-items:center; justify-content:space-between;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                            <input type="checkbox" <?php echo $g['is_checked'] ? 'checked' : ''; ?> onclick="window.location.href='my_dashboard.php?toggle_grocery=<?php echo $g['id']; ?>'">
                            <span style="<?php echo $g['is_checked'] ? 'text-decoration:line-through; color:var(--text-muted);' : ''; ?>">
                                <?php echo htmlspecialchars($g['item_name']); ?>
                            </span>
                        </label>
                        <a href="my_delete_grocery.php?id=<?php echo $g['id']; ?>" style="color:var(--danger); font-size:12px; text-decoration:none;" onclick="return confirm('Delete this grocery item?');">✕</a>
                    </li>
                    <?php endforeach; else: ?>
                    <li style="color:var(--text-muted); text-align:center; padding:10px;">No grocery items.</li>
                    <?php endif; ?>
                </ul>
            </div>
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