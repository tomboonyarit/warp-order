<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TomOrder\Pos\Database\Connection;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../routes/api.php';