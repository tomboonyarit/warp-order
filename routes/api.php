<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TomOrder\Pos\Database\Connection;

header('Content-Type: application/json');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$api = '/api';
if (strpos($path, $api) === 0) {
    $route = substr($path, strlen($api));
    
    switch ($route) {
        case '/login':
            require_once __DIR__ . '/../src/Controllers/AuthController.php';
            $controller = new \TomOrder\Pos\Controllers\AuthController();
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                echo $controller->login($input);
            }
            break;
            
        case '/products':
            require_once __DIR__ . '/../src/Controllers/ProductController.php';
            $controller = new \TomOrder\Pos\Controllers\ProductController();
            if ($method === 'GET') {
                echo $controller->index();
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                echo $controller->store($input);
            }
            break;
            
        case '/orders':
            require_once __DIR__ . '/../src/Controllers/OrderController.php';
            $controller = new \TomOrder\Pos\Controllers\OrderController();
            if ($method === 'GET') {
                $status = $_GET['status'] ?? null;
                echo $controller->index($status);
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                echo $controller->store($input);
            }
            break;
            
        case '/orders/status':
            require_once __DIR__ . '/../src/Controllers/OrderController.php';
            $controller = new \TomOrder\Pos\Controllers\OrderController();
            if ($method === 'PUT') {
                $input = json_decode(file_get_contents('php://input'), true);
                $pathParts = explode('/', $route);
                $id = $pathParts[2] ?? null;
                echo $controller->updateStatus($id, $input);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}