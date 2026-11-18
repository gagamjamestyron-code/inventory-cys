<?php
require_once 'includes/db.php';

$search = $_GET['search'] ?? '';
$lowStockOnly = isset($_GET['low_stock']);
$allItems = $pdo->query('SELECT * FROM items')->fetchAll();

$items = [];
foreach ($allItems as $item) {
    $low = isLowStock($item['quantity'], $item['restock_level']);
    if ($lowStockOnly && !$low) continue;
    if ($search !== '' && stripos($item['name'] . ' ' . $item['category'], $search) === false) continue;
    $items[] = $item;
}
usort($items, fn($a, $b) => $b['id'] <=> $a['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyInventory - Items</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-shell">
    <div class="sidebar">
        <div><div class="logo-area">🛒 MyInventory <span class="logo-sub">Track • Manage • Restock</span></div><ul>
            <li><a href="my_dashboard.php">📊 Dashboard</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSubmenu(event)">📦 Items ▾</a><ul class="submenu" id="itemSubmenu"><li><a href="my_items.php" class="active">View Items</a></li><li><a href="my_add.php">Add Item</a></li></ul></li>
            <li><a href="my_history.php">🕒 History</a></li>
        </ul></div>
        <div><ul><li><a href="settings.php">⚙️ Settings</a></li><li><a href="logout.php">🚪 Logout</a></li></ul></div>
    </div>

    <main class="main-content">
        <div class="header-top"><div><h1>Items Management</h1><p><?= $lowStockOnly ? 'Showing items that need restocking.' : 'Manage, search, edit, and delete inventory stocks.' ?></p></div><a href="my_add.php" class="btn-action header-action">+ Add New Item</a></div>

        <div class="section-box">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search items or categories..." value="<?= htmlspecialchars($search) ?>">
                <?php if ($lowStockOnly): ?><input type="hidden" name="low_stock" value="1"><?php endif; ?>
                <button type="submit" class="btn-action">Search</button>
                <?php if ($lowStockOnly): ?><a href="my_items.php" class="btn-action delete">Show All</a><?php else: ?><a href="my_items.php?low_stock=1" class="btn-action delete">Low Stock Only</a><?php endif; ?>
            </form>

            <div class="table-responsive"><table><thead><tr><th>Item</th><th>Category</th><th>Quantity</th><th>Price</th><th>Date Added</th><th>Restock Level</th><th>Status</th><th>Actions</th></tr></thead><tbody>
            <?php if ($items): foreach ($items as $item): $low = isLowStock($item['quantity'], $item['restock_level']); ?>
                <tr class="<?= $low ? 'low-stock-row' : '' ?>" <?= $low && $lowStockOnly ? 'id="low-stock-highlight"' : '' ?>><td><strong><?= htmlspecialchars($item['name']) ?></strong></td><td><span class="pill"><?= htmlspecialchars($item['category']) ?></span></td><td><?= htmlspecialchars($item['quantity']) ?></td><td>₱<?= number_format((float)$item['price'], 2) ?></td><td><?= formatAppDate($item['date_added']) ?></td><td><?= htmlspecialchars($item['restock_level']) ?></td><td><span class="status-badge <?= $low ? 'low' : 'normal' ?>"><?= $low ? 'Low Stock' : 'Normal' ?></span></td><td><a href="my_edit.php?id=<?= $item['id'] ?>" class="btn-action">Edit</a><button type="button" class="btn-action delete js-delete" data-name="<?= htmlspecialchars($item['name']) ?>" data-url="my_delete.php" data-id="<?= $item['id'] ?>">Delete</button></td></tr>
            <?php endforeach; else: ?><tr><td colspan="8" class="empty-cell">No items found.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div>
    </main>
</div>

<div class="modal-backdrop" id="deleteModal"><div class="modal-card"><div class="modal-icon delete-icon">🗑️</div><h2>Delete Item</h2><p id="deleteMessage">Are you sure you want to delete this item?</p><form id="deleteForm" method="POST"><input type="hidden" name="id" id="deleteId"><div class="modal-actions"><button type="submit" class="btn-action delete">Delete</button><button type="button" class="btn-action" onclick="closeDeleteModal()">Cancel</button></div></form></div></div>
<script>
function toggleSubmenu(e){e.preventEvent?.();e.preventDefault();const s=document.getElementById('itemSubmenu');s.style.display=s.style.display==='block'?'none':'block';}
function closeDeleteModal(){document.getElementById('deleteModal').classList.remove('show');}
document.querySelectorAll('.js-delete').forEach(b=>b.addEventListener('click',()=>{document.getElementById('deleteId').value=b.dataset.id;document.getElementById('deleteForm').action=b.dataset.url;document.getElementById('deleteMessage').textContent=`Delete “${b.dataset.name}”? This cannot be undone.`;document.getElementById('deleteModal').classList.add('show');}));
</script>
</body>
</html>
