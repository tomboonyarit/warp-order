<?php

namespace TomOrder\Pos\Database\Migrations;

class CreateOrdersTable
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                queue_number INTEGER NOT NULL,
                customer_name TEXT,
                distinctive_notes TEXT,
                remark TEXT,
                status TEXT NOT NULL DEFAULT 'pending',
                user_id INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        \TomOrder\Pos\Database\Connection::getInstance()->exec($sql);
    }

    public function down(): void
    {
        \TomOrder\Pos\Database\Connection::getInstance()->exec("DROP TABLE IF EXISTS orders");
    }
}