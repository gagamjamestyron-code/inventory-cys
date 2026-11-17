<?php
require_once 'includes/db.php';
$logs = $pdo->query("SELECT * FROM history ORDER BY timestamp DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History - MyInventory</title>
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
                <li><a href="my_history.php" class="active">🕒 History</a></li>
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
                <h1>Activity History Log</h1>
                <p>Track all additions, updates, and deletions in the system.</p>
            </div>
        </div>

        <div class="section-box">
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Item Name</th>
                        <th>Change</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($logs) > 0): foreach ($logs as $log): ?>
                    <tr>
                        <td style="color:var(--primary); font-weight:600;"><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($log['quantity_change']); ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($log['timestamp'])); ?></td>
                        <td>
                            <a href="my_delete_history.php?id=<?php echo $log['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this history log?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">No history logs found.</td></tr>
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