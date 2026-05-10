<?php

$database = $_ENV['DB_DATABASE'] ?? dirname(__DIR__) . '/database/app.db';

if (!preg_match('#^(?:[A-Za-z]:[\\\\/]|/|\\\\\\\\)#', $database)) {
    $database = dirname(__DIR__) . '/' . ltrim($database, './\\');
}

return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite',
    'database' => $database,
];
