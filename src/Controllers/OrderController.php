<?php

namespace TomOrder\Pos\Controllers;

use TomOrder\Pos\Database\Connection;

class OrderController
{
    public function index(string $status = null): string
    {
        $db = Connection::getInstance();
        
        if ($status) {
            $stmt = $db->prepare("
                SELECT o.*, COUNT(oi.id) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                WHERE o.status = ? 
                GROUP BY o.id 
                ORDER BY o.queue_number
            ");
            $stmt->execute([$status]);
        } else {
            $stmt = $db->query("
                SELECT o.*, COUNT(oi.id) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                GROUP BY o.id 
                ORDER BY o.queue_number
            ");
        }
        
        $orders = [];
        while ($row = $stmt->fetch()) {
            $orderId = $row['id'];
            $itemStmt = $db->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $itemStmt->execute([$orderId]);
            $row['items'] = $itemStmt->fetchAll();
            $orders[] = $row;
        }
        
        return json_encode($orders);
    }
    
    public function store(array $data): string
    {
        $db = Connection::getInstance();
        $db->beginTransaction();
        
        try {
            $stmt = $db->query("SELECT COALESCE(MAX(queue_number), 0) + 1 FROM orders");
            $queueNumber = $stmt->fetchColumn();
            
            $stmt = $db->prepare("INSERT INTO orders (queue_number, customer_name, distinctive_notes, remark, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([
                $queueNumber,
                $data['customer_name'] ?? '',
                $data['distinctive_notes'] ?? '',
                $data['remark'] ?? ''
            ]);
            $orderId = $db->lastInsertId();
            
            foreach ($data['items'] as $item) {
                $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }
            
            $db->commit();
            
            return json_encode(['id' => $orderId, 'queue_number' => $queueNumber, 'status' => 'pending']);
        } catch (Exception $e) {
            $db->rollBack();
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    public function updateStatus(int $id, array $data): string
    {
        $status = $data['status'] ?? 'pending';
        
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        return json_encode(['success' => true]);
    }
}