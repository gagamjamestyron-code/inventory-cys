<?php
date_default_timezone_set('Asia/Manila');

try {
    $databaseUrl = getenv('DATABASE_URL') ?: '';

    if ($databaseUrl !== '') {
        $url = parse_url($databaseUrl);
        $host = $url['host'] ?? 'localhost';
        $port = $url['port'] ?? 5432;
        $dbname = ltrim($url['path'] ?? '/inventory', '/');
        $username = $url['user'] ?? '';
        $password = $url['pass'] ?? '';

        $pdo = new PDO(
            "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
        $pdo->exec("SET TIME ZONE 'Asia/Manila'");
    } else {
        $dbPath = __DIR__ . '/../database/my_inventory.db';
        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
} catch (Throwable $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

function quantityNumber($quantity): float
{
    if (preg_match('/[-+]?(?:\d+(?:\.\d*)?|\.\d+)/', (string) $quantity, $match)) {
        return (float) $match[0];
    }
    return 0;
}

function isLowStock($quantity, $restockLevel): bool
{
    return quantityNumber($quantity) <= (float) $restockLevel;
}

function formatAppDate($value): string
{
    if (!$value) return '—';
    $timestamp = strtotime((string) $value);
    return $timestamp ? date('M d, Y h:i A', $timestamp) : '—';
}
