<?php
date_default_timezone_set('Asia/Manila');

try {
    $dbPath = __DIR__ . '/../database/my_inventory.db';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA timezone = '+08:00';");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>