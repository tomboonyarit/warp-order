<?php

namespace TomOrder\Pos\Database\Migrations;

class CreateOrderItemsTable
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                price REAL NOT NULL
            )
        ";
        \TomOrder\Pos\Database\Connection::getInstance()->exec($sql);
    }

    public function down(): void
    {
        \TomOrder\Pos\Database\Connection::getInstance()->exec("DROP TABLE IF EXISTS order_items");
    }
}