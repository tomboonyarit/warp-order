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

        case '/setup':
            require_once __DIR__ . '/../src/Controllers/SetupController.php';
            $controller = new \TomOrder\Pos\Controllers\SetupController();
            if ($method === 'GET') {
                echo $controller->index();
            }
            break;

        case '/setup/menu-categories':
            require_once __DIR__ . '/../src/Controllers/SetupController.php';
            $controller = new \TomOrder\Pos\Controllers\SetupController();
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                echo $controller->storeMenuCategory($input);
            }
            break;

        case '/setup/note-groups':
            require_once __DIR__ . '/../src/Controllers/SetupController.php';
            $controller = new \TomOrder\Pos\Controllers\SetupController();
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                echo $controller->storeNoteGroup($input);
            }
            break;

        case '/setup/note-options':
            require_once __DIR__ . '/../src/Controllers/SetupController.php';
            $controller = new \TomOrder\Pos\Controllers\SetupController();
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                echo $controller->storeNoteOption($input);
            }
            break;
            
        default:
            if (preg_match('#^/setup/menu-categories/(\d+)$#', $route, $matches)) {
                require_once __DIR__ . '/../src/Controllers/SetupController.php';
                $controller = new \TomOrder\Pos\Controllers\SetupController();
                if ($method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $controller->updateMenuCategory((int) $matches[1], $input);
                } elseif ($method === 'DELETE') {
                    echo $controller->archiveMenuCategory((int) $matches[1]);
                }
            } elseif (preg_match('#^/setup/note-groups/(\d+)$#', $route, $matches)) {
                require_once __DIR__ . '/../src/Controllers/SetupController.php';
                $controller = new \TomOrder\Pos\Controllers\SetupController();
                if ($method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $controller->updateNoteGroup((int) $matches[1], $input);
                } elseif ($method === 'DELETE') {
                    echo $controller->archiveNoteGroup((int) $matches[1]);
                }
            } elseif (preg_match('#^/setup/note-options/(\d+)$#', $route, $matches)) {
                require_once __DIR__ . '/../src/Controllers/SetupController.php';
                $controller = new \TomOrder\Pos\Controllers\SetupController();
                if ($method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $controller->updateNoteOption((int) $matches[1], $input);
                } elseif ($method === 'DELETE') {
                    echo $controller->archiveNoteOption((int) $matches[1]);
                }
            } elseif (preg_match('#^/products/(\d+)$#', $route, $matches)) {
                require_once __DIR__ . '/../src/Controllers/ProductController.php';
                $controller = new \TomOrder\Pos\Controllers\ProductController();
                if ($method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $controller->update((int) $matches[1], $input);
                } elseif ($method === 'DELETE') {
                    echo $controller->archive((int) $matches[1]);
                }
            } elseif (preg_match('#^/orders/status/(\d+)$#', $route, $matches)) {
                require_once __DIR__ . '/../src/Controllers/OrderController.php';
                $controller = new \TomOrder\Pos\Controllers\OrderController();
                if ($method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $controller->updateStatus($matches[1], $input);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
            }
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
