<?php

namespace TomOrder\Pos\Controllers;

use TomOrder\Pos\Database\Connection;

class ProductController
{
    public function index(): string
    {
        $db = Connection::getInstance();
        $stmt = $db->query("SELECT * FROM products WHERE is_active = 1 ORDER BY category, name");
        $products = $stmt->fetchAll();
        
        return json_encode($products);
    }
    
    public function store(array $data): string
    {
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        $category = $data['category'] ?? null;
        
        $db = Connection::getInstance();
        $stmt = $db->prepare("INSERT INTO products (name, price, category, is_active) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name, $price, $category]);
        
        return json_encode(['id' => $db->lastInsertId(), 'name' => $name, 'price' => $price, 'category' => $category, 'is_active' => 1]);
    }

    public function update(int $id, array $data): string
    {
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        $category = $data['category'] ?? null;

        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, category = ? WHERE id = ?");
        $stmt->execute([$name, $price, $category, $id]);

        return json_encode(['id' => $id, 'name' => $name, 'price' => $price, 'category' => $category]);
    }

    public function archive(int $id): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return json_encode(['success' => true]);
    }
}
