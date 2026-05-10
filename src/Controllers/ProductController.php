<?php

namespace TomOrder\Pos\Controllers;

use TomOrder\Pos\Database\Connection;

class ProductController
{
    public function index(): string
    {
        $db = Connection::getInstance();
        $stmt = $db->query("SELECT * FROM products WHERE is_active = true ORDER BY name");
        $products = $stmt->fetchAll();
        
        return json_encode($products);
    }
    
    public function store(array $data): string
    {
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        $category = $data['category'] ?? null;
        
        $db = Connection::getInstance();
        $stmt = $db->prepare("INSERT INTO products (name, price, category) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $category]);
        
        return json_encode(['id' => $db->lastInsertId(), 'name' => $name, 'price' => $price]);
    }
}