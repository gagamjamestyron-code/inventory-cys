<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Item - MyInventory</title>
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
                        <li><a href="my_add.php" class="active">Add Item</a></li>
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
                <h1>Add New Item</h1>
                <p>Fill in the product details below.</p>
            </div>
        </div>

        <div class="form-card">
            <form action="my_save.php" method="POST">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="name" placeholder="e.g., Rice, Milk, Eggs" required>
                </div>
                
                <div class="form-group">
                    <label>Category (Type any category)</label>
                    <input type="text" name="category" placeholder="e.g., Pantry, Beverages, Snacks" required>
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input type="text" name="quantity" placeholder="e.g., 5 kg, 2 cartons" required>
                </div>

                <div class="form-group">
                    <label>Price (₱)</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label>Restock Level Threshold</label>
                    <input type="number" name="restock_level" placeholder="e.g., 2" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-action" style="padding: 12px 24px; font-size:14px;">Save Item</button>
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