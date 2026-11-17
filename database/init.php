<?php
require_once __DIR__ . '/../includes/db.php';
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            category TEXT NOT NULL,
            quantity TEXT NOT NULL,
            price REAL NOT NULL,
            restock_level INTEGER NOT NULL,
            date_added DATETIME DEFAULT (datetime('now', '+8 hours'))
        );

        CREATE TABLE IF NOT EXISTS history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            action TEXT NOT NULL,
            item_name TEXT NOT NULL,
            quantity_change TEXT,
            timestamp DATETIME DEFAULT (datetime('now', '+8 hours'))
        );

        CREATE TABLE IF NOT EXISTS grocery_list (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            item_name TEXT NOT NULL,
            is_checked INTEGER DEFAULT 0
        );
    ");
    echo "Database tables created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>