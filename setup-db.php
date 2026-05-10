<?php
require __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('sqlite:' . __DIR__ . '/database/app.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, password TEXT, role TEXT)');
$pdo->exec('CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, price REAL, category TEXT, is_active INTEGER DEFAULT 1)');
$pdo->exec('CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, queue_number INTEGER, customer_name TEXT, distinctive_notes TEXT, remark TEXT, status TEXT)');
$pdo->exec('CREATE TABLE IF NOT EXISTS order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER, product_id INTEGER, quantity INTEGER, price REAL)');
$pdo->exec('CREATE TABLE IF NOT EXISTS menu_categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL UNIQUE, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1)');
$pdo->exec('CREATE TABLE IF NOT EXISTS note_groups (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, prompt TEXT, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1)');
$pdo->exec('CREATE TABLE IF NOT EXISTS note_options (id INTEGER PRIMARY KEY AUTOINCREMENT, group_id INTEGER NOT NULL, label TEXT NOT NULL, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1)');

function addColumnIfMissing(PDO $pdo, string $table, string $column, string $definition): void
{
    $columns = $pdo->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_COLUMN, 1);

    if (!in_array($column, $columns, true)) {
        $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    }
}

addColumnIfMissing($pdo, 'products', 'category', 'TEXT');
addColumnIfMissing($pdo, 'products', 'is_active', 'INTEGER DEFAULT 1');
addColumnIfMissing($pdo, 'orders', 'distinctive_notes', 'TEXT');
addColumnIfMissing($pdo, 'orders', 'remark', 'TEXT');

if ((int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO products (name, price) VALUES ('ข้าวมันไก่', 50)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('ผัดไทย', 60)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('ส้มตำ', 45)");
}

if ((int) $pdo->query('SELECT COUNT(*) FROM menu_categories')->fetchColumn() === 0) {
    $stmt = $pdo->prepare('INSERT INTO menu_categories (name, sort_order) VALUES (?, ?)');
    foreach (['เมนูหลัก', 'อาหารจานเดียว', 'เครื่องดื่ม', 'ของหวาน'] as $index => $categoryName) {
        $stmt->execute([$categoryName, $index + 1]);
    }
}

if ((int) $pdo->query('SELECT COUNT(*) FROM note_groups')->fetchColumn() === 0) {
    $groups = [
        ['เสื้อ', 'เลือกสีเสื้อ', ['ขาว', 'ดำ', 'แดง', 'น้ำเงิน', 'เหลือง', 'เขียว']],
        ['กางเกง', 'เลือกสีกางเกง', ['ดำ', 'ยีนส์', 'น้ำตาล', 'เทา', 'ขาว']],
        ['หมวก', 'เลือกสีหมวก', ['ดำ', 'ขาว', 'แดง', 'น้ำเงิน', 'ไม่มีหมวก']],
        ['ตำแหน่ง', 'เลือกจุดนั่ง/ยืน', ['โต๊ะ 1', 'โต๊ะ 2', 'โต๊ะ 3', 'หน้าร้าน', 'นอกร้าน']],
        ['อื่นๆ', 'เลือกจุดจำง่าย', ['ใส่แว่น', 'มากับเด็ก', 'ถือร่ม', 'กระเป๋าใหญ่']],
    ];

    $groupStmt = $pdo->prepare('INSERT INTO note_groups (name, prompt, sort_order) VALUES (?, ?, ?)');
    $optionStmt = $pdo->prepare('INSERT INTO note_options (group_id, label, sort_order) VALUES (?, ?, ?)');

    foreach ($groups as $groupIndex => $group) {
        $groupStmt->execute([$group[0], $group[1], $groupIndex + 1]);
        $groupId = $pdo->lastInsertId();

        foreach ($group[2] as $optionIndex => $option) {
            $optionStmt->execute([$groupId, $option, $optionIndex + 1]);
        }
    }
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$stmt->execute(['admin']);

if ((int) $stmt->fetchColumn() === 0) {
    $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
    $stmt->execute(['admin', password_hash('admin', PASSWORD_DEFAULT), 'admin']);
}

echo "Database created successfully!\n";
