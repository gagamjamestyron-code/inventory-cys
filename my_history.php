<?php
require_once 'includes/db.php';
$logs = $pdo->query('SELECT * FROM history ORDER BY timestamp DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - MyInventory</title>
    <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>
<div class="app-shell">
    <nav class="sidebar">
        <div><div class="logo-area">🛒 MyInventory <span class="logo-sub">Track • Manage • Restock</span></div><ul>
            <li><a href="my_dashboard.php">📊 Dashboard</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSubmenu(event)">📦 Items ▾</a><ul class="submenu" id="itemSubmenu"><li><a href="my_items.php">View Items</a></li><li><a href="my_add.php">Add Item</a></li></ul></li>
            <li><a href="my_history.php" class="active">🕒 History</a></li>
        </ul></div>
        <div><ul><li><a href="settings.php">⚙️ Settings</a></li><li><a href="logout.php">🚪 Logout</a></li></ul></div>
    </nav>
    <main class="main-content">
        <div class="header-top"><div><h1>Activity History Log</h1><p>Track all additions, updates, and deletions in the system.</p></div></div>
        <div class="section-box"><div class="table-responsive"><table><thead><tr><th>Action</th><th>Item Name</th><th>Change</th><th>Timestamp</th><th>Actions</th></tr></thead><tbody>
        <?php if ($logs): foreach ($logs as $log): ?>
            <tr><td class="primary-text"><?= htmlspecialchars($log['action']) ?></td><td><?= htmlspecialchars($log['item_name']) ?></td><td><?= htmlspecialchars($log['quantity_change'] ?? '') ?></td><td><?= formatAppDate($log['timestamp']) ?></td><td><button type="button" class="btn-action delete js-delete" data-name="<?= htmlspecialchars($log['item_name']) ?>" data-url="my_delete_history.php" data-id="<?= (int)$log['id'] ?>">Delete</button></td></tr>
        <?php endforeach; else: ?><tr><td colspan="5" class="empty-cell">No history logs found.</td></tr><?php endif; ?>
        </tbody></table></div></div>
    </main>
</div>
<div class="modal-backdrop" id="deleteModal"><div class="modal-card"><div class="modal-icon delete-icon">🗑️</div><h2>Delete Activity</h2><p id="deleteMessage">Are you sure you want to delete this history entry?</p><form id="deleteForm" method="POST"><input type="hidden" name="id" id="deleteId"><div class="modal-actions"><button type="submit" class="btn-action delete">Delete</button><button type="button" class="btn-action" onclick="closeDeleteModal()">Cancel</button></div></form></div></div>
<script>
function toggleSubmenu(e){e.preventDefault();const s=document.getElementById('itemSubmenu');s.style.display=s.style.display==='block'?'none':'block';}
function closeDeleteModal(){document.getElementById('deleteModal').classList.remove('show');}
document.querySelectorAll('.js-delete').forEach(b=>b.addEventListener('click',()=>{document.getElementById('deleteId').value=b.dataset.id;document.getElementById('deleteForm').action=b.dataset.url;document.getElementById('deleteMessage').textContent=`Delete the activity for “${b.dataset.name}”?`;document.getElementById('deleteModal').classList.add('show');}));
document.getElementById('deleteModal').addEventListener('click',e=>{if(e.target===e.currentTarget)closeDeleteModal();});
</script>
</body>
</html>
