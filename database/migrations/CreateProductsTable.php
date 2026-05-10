<?php

namespace TomOrder\Pos\Database\Migrations;

class CreateProductsTable
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS products (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) NOT NULL DEFAULT 0,
                category VARCHAR(100),
                is_active BOOLEAN DEFAULT true,
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