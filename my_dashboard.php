<?php
require_once 'includes/db.php';
session_start();

if (isset($_POST['add_grocery'])) {
    $name = trim($_POST['grocery_name'] ?? '');
    if ($name !== '') {
        $stmt = $pdo->prepare('INSERT INTO grocery_list (item_name) VALUES (:name)');
        $stmt->execute([':name' => $name]);
    }
    header('Location: my_dashboard.php');
    exit;
}

if (isset($_GET['toggle_grocery'])) {
    $stmt = $pdo->prepare('UPDATE grocery_list SET is_checked = CASE WHEN is_checked = 1 THEN 0 ELSE 1 END WHERE id = :id');
    $stmt->execute([':id' => (int) $_GET['toggle_grocery']]);
    header('Location: my_dashboard.php');
    exit;
}

if (isset($_GET['dismiss_alert'])) {
    $_SESSION['low_stock_alert_dismissed'] = true;
    header('Location: my_dashboard.php');
    exit;
}

$allItems = $pdo->query('SELECT * FROM items')->fetchAll();
$totalItems = count($allItems);
$lowStockCount = count(array_filter($allItems, fn($item) => isLowStock($item['quantity'], $item['restock_level'])));
$categoriesCount = count(array_unique(array_column($allItems, 'category')));

// Show the popup only at 4+ low-stock items, once per alert cycle until dismissed.
// Reset the dismissal when inventory falls below the popup threshold so a later alert can show.
if ($lowStockCount < 4) {
    unset($_SESSION['low_stock_alert_dismissed']);
}
$showLowStockAlert = $lowStockCount >= 4 && empty($_SESSION['low_stock_alert_dismissed']);

$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$items = array_values(array_filter($allItems, function ($item) use ($search, $categoryFilter) {
    $matchesSearch = $search === '' || stripos($item['name'] . ' ' . $item['category'], $search) !== false;
    $matchesCategory = $categoryFilter === '' || stripos($item['category'], $categoryFilter) !== false;
    return $matchesSearch && $matchesCategory;
}));
usort($items, fn($a, $b) => $b['id'] <=> $a['id']);
$items = array_slice($items, 0, 5);

$history = $pdo->query('SELECT * FROM history ORDER BY timestamp DESC LIMIT 3')->fetchAll();
$groceries = $pdo->query('SELECT * FROM grocery_list ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyInventory - Dashboard</title>
    <link rel="stylesheet" href="css/style.css?v=5">
</head>
<body>
<div class="app-shell">
    <nav class="sidebar">
        <div>
            <div class="logo-area">🛒 MyInventory <span class="logo-sub">Track • Manage • Restock</span></div>
            <ul>
                <li><a href="my_dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="javascript:void(0)" onclick="toggleSubmenu(event)">📦 Items ▾</a>
                    <ul class="submenu" id="itemSubmenu"><li><a href="my_items.php">View Items</a></li><li><a href="my_add.php">Add Item</a></li></ul>
                </li>
                <li><a href="my_history.php">🕒 History</a></li>
            </ul>
        </div>
        <div class="sidebar-bottom"><ul><li><a href="settings.php">⚙️ Settings</a></li><li><a href="logout.php">🚪 Logout</a></li></ul></div>
    </nav>

    <main class="main-content">
        <div class="header-top"><div><h1>Dashboard 👋</h1><p>Here's your inventory overview!</p></div></div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon blue">📦</div><div class="stat-info"><h3>Total Items</h3><div class="value"><?= $totalItems ?></div></div></div>
            <div class="stat-card"><div class="stat-icon red">⚠️</div><div class="stat-info"><h3>Low Stock</h3><div class="value low-value"><?= $lowStockCount ?></div></div></div>
            <div class="stat-card"><div class="stat-icon green">🛒</div><div class="stat-info"><h3>Categories</h3><div class="value success-value"><?= $categoriesCount ?></div></div></div>
            <div class="alert-banner <?= $lowStockCount === 0 ? 'normal-alert' : 'low-alert-banner' ?>">
                <div><h4><?= $lowStockCount ? '⚠️ Low Stock Alert' : '✅ Stock Status Normal' ?></h4><p><?= $lowStockCount ? $lowStockCount . ' item' . ($lowStockCount === 1 ? ' is' : 's are') . ' running low!' : 'All items have sufficient stock.' ?></p></div>
                <a href="my_items.php<?= $lowStockCount ? '?low_stock=1' : '' ?>" class="btn-alert">View Items →</a>
            </div>
        </div>

        <section class="section-box filter-box"><form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search items..." value="<?= htmlspecialchars($search) ?>">
            <input type="text" name="category" placeholder="Filter category..." value="<?= htmlspecialchars($categoryFilter) ?>">
            <button type="submit" class="btn-action">Filter</button>
        </form></section>

        <section class="section-box">
            <div class="section-header"><h3>📦 Your Items</h3><a href="my_items.php" class="btn-action">View All</a></div>
            <div class="table-responsive"><table><thead><tr><th>Item</th><th>Category</th><th>Quantity</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
            <?php if ($items): foreach ($items as $item): $low = isLowStock($item['quantity'], $item['restock_level']); ?>
                <tr class="<?= $low ? 'low-stock-row' : '' ?>"><td><strong><?= htmlspecialchars($item['name']) ?></strong></td><td><span class="pill"><?= htmlspecialchars($item['category']) ?></span></td><td><?= htmlspecialchars($item['quantity']) ?></td><td>₱<?= number_format((float) $item['price'], 2) ?></td><td><span class="status-badge <?= $low ? 'low' : 'normal' ?>"><?= $low ? 'Low Stock' : 'Normal' ?></span></td><td class="action-cell"><a href="my_edit.php?id=<?= (int) $item['id'] ?>" class="btn-action">Edit</a><button type="button" class="btn-action delete js-delete" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-url="my_delete.php" data-id="<?= (int) $item['id'] ?>">Delete</button></td></tr>
            <?php endforeach; else: ?><tr><td colspan="6" class="empty-cell">No items found.</td></tr><?php endif; ?>
            </tbody></table></div>
        </section>

        <div class="bottom-grid">
            <section class="section-box">
                <div class="section-header"><h3>🕒 Recent Activity</h3><a href="my_history.php" class="text-link">View All →</a></div>
                <div class="table-responsive"><table><thead><tr><th>Action</th><th>Item</th><th>Change</th><th>Date &amp; Time</th></tr></thead><tbody>
                <?php if ($history): foreach ($history as $entry): ?><tr><td class="primary-text"><?= htmlspecialchars($entry['action']) ?></td><td><?= htmlspecialchars($entry['item_name']) ?></td><td><?= htmlspecialchars($entry['quantity_change'] ?? '') ?></td><td><?= formatAppDate($entry['timestamp']) ?></td></tr><?php endforeach; else: ?><tr><td colspan="4" class="empty-cell">No recent activity.</td></tr><?php endif; ?>
                </tbody></table></div>
            </section>

            <section class="section-box grocery-section">
                <div class="section-header"><h3>🛒 Grocery List</h3></div>
                <form method="POST" class="grocery-form"><input type="text" name="grocery_name" placeholder="Add item..." required><button type="submit" name="add_grocery" class="btn-action">Add</button></form>
                <ul class="grocery-items">
                    <?php if ($groceries): foreach ($groceries as $grocery): ?><li><label><input type="checkbox" <?= $grocery['is_checked'] ? 'checked' : '' ?> onchange="window.location.href='my_dashboard.php?toggle_grocery=<?= (int) $grocery['id'] ?>'"><span class="<?= $grocery['is_checked'] ? 'checked-text' : '' ?>"><?= htmlspecialchars($grocery['item_name']) ?></span></label><button type="button" class="grocery-delete js-delete" aria-label="Delete <?= htmlspecialchars($grocery['item_name'], ENT_QUOTES) ?>" data-name="<?= htmlspecialchars($grocery['item_name'], ENT_QUOTES) ?>" data-url="my_delete_grocery.php" data-id="<?= (int) $grocery['id'] ?>">×</button></li><?php endforeach; else: ?><li class="grocery-empty">Your grocery list is empty.</li><?php endif; ?>
                </ul>
            </section>
        </div>
    </main>
</div>

<?php if ($showLowStockAlert): ?><div class="modal-backdrop show" id="lowStockModal" role="dialog" aria-modal="true" aria-labelledby="lowStockTitle"><div class="modal-card"><div class="modal-icon">⚠️</div><h2 id="lowStockTitle">Low Stock Alert</h2><p><strong><?= $lowStockCount ?></strong> <?= $lowStockCount === 1 ? 'item needs' : 'items need' ?> restocking.</p><div class="modal-actions"><a href="my_items.php?low_stock=1" class="btn-action">View Low-Stock Items</a><a href="my_dashboard.php?dismiss_alert=1" class="btn-action delete">Dismiss</a></div></div></div><?php endif; ?>
<div class="modal-backdrop" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteTitle"><div class="modal-card"><div class="modal-icon delete-icon">🗑️</div><h2 id="deleteTitle">Confirm Delete</h2><p id="deleteMessage">Are you sure you want to delete this?</p><form id="deleteForm" method="POST"><input type="hidden" name="id" id="deleteId"><div class="modal-actions"><button type="submit" class="btn-action delete">Delete</button><button type="button" class="btn-action" onclick="closeDeleteModal()">Cancel</button></div></form></div></div>
<script>
function toggleSubmenu(e){e.preventDefault();const menu=document.getElementById('itemSubmenu');menu.style.display=menu.style.display==='block'?'none':'block';}
function closeDeleteModal(){document.getElementById('deleteModal').classList.remove('show');}
document.querySelectorAll('.js-delete').forEach(button=>button.addEventListener('click',()=>{document.getElementById('deleteId').value=button.dataset.id;document.getElementById('deleteForm').action=button.dataset.url;document.getElementById('deleteMessage').textContent=`Delete “${button.dataset.name}”? This cannot be undone.`;document.getElementById('deleteModal').classList.add('show');}));
document.querySelectorAll('.modal-backdrop').forEach(backdrop=>backdrop.addEventListener('click',event=>{if(event.target===backdrop)backdrop.classList.remove('show');}));
</script>
</body>
</html>
