<?php

namespace TomOrder\Pos\Database\Migrations;

class CreateOrdersTable
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS orders (
                id SERIAL PRIMARY KEY,
                queue_number INTEGER NOT NULL UNIQUE,
                customer_name VARCHAR(255),
                distinctive_notes TEXT,
                remark TEXT,
                status VARCHAR(50) NOT NULL DEFAULT 'pending',
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