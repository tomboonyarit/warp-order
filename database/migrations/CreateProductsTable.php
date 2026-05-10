<?php

namespace TomOrder\Pos\Database\Migrations;

class CreateProductsTable
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                price REAL NOT NULL DEFAULT 0,
                category TEXT,
                is_active INTEGER DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        \TomOrder\Pos\Database\Connection::getInstance()->exec($sql);
    }

    public function down(): void
    {
        \TomOrder\Pos\Database\Connection::getInstance()->exec("DROP TABLE IF EXISTS products");
    }
}