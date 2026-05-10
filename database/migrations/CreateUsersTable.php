<?php

namespace TomOrder\Pos\Database\Migrations;

class CreateUsersTable
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL DEFAULT 'staff',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        \TomOrder\Pos\Database\Connection::getInstance()->exec($sql);
    }

    public function down(): void
    {
        \TomOrder\Pos\Database\Connection::getInstance()->exec("DROP TABLE IF EXISTS users");
    }
}