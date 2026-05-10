<?php
require __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('sqlite:' . __DIR__ . '/database/app.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, password TEXT, role TEXT)');
$pdo->exec('CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, price REAL, category TEXT)');
$pdo->exec('CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, queue_number INTEGER, customer_name TEXT, distinctive_notes TEXT, remark TEXT, status TEXT)');
$pdo->exec('CREATE TABLE IF NOT EXISTS order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER, product_id INTEGER, quantity INTEGER, price REAL)');

function addColumnIfMissing(PDO $pdo, string $table, string $column, string $definition): void
{
    $columns = $pdo->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_COLUMN, 1);

    if (!in_array($column, $columns, true)) {
        $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    }
}

addColumnIfMissing($pdo, 'products', 'category', 'TEXT');
addColumnIfMissing($pdo, 'orders', 'distinctive_notes', 'TEXT');
addColumnIfMissing($pdo, 'orders', 'remark', 'TEXT');

if ((int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO products (name, price) VALUES ('ข้าวมันไก่', 50)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('ผัดไทย', 60)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('ส้มตำ', 45)");
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$stmt->execute(['admin']);

if ((int) $stmt->fetchColumn() === 0) {
    $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
    $stmt->execute(['admin', password_hash('admin', PASSWORD_DEFAULT), 'admin']);
}

echo "Database created successfully!\n";
