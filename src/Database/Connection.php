<?php

namespace TomOrder\Pos\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            
            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int) $e->getCode());
            }
        }
        
        return self::$instance;
    }
}