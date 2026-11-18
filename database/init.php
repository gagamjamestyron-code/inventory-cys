<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $databaseUrl = getenv('DATABASE_URL') ?: '';

    if ($databaseUrl !== '') {
        $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS items (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    quantity TEXT NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    restock_level INTEGER NOT NULL,
    date_added TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS history (
    id SERIAL PRIMARY KEY,
    action TEXT NOT NULL,
    item_name TEXT NOT NULL,
    quantity_change TEXT,
    timestamp TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS grocery_list (
    id SERIAL PRIMARY KEY,
    item_name TEXT NOT NULL,
    is_checked INTEGER DEFAULT 0
);
SQL
        );
    } else {
        $pdo->exec(<<<'SQL'
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
SQL
        );
    }
    echo 'Database tables are ready.';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Database initialization failed: ' . htmlspecialchars($e->getMessage());
}
