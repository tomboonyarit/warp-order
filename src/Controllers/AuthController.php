<?php

namespace TomOrder\Pos\Controllers;

use TomOrder\Pos\Database\Connection;
use Firebase\JWT\JWT;

class AuthController
{
    public function login(array $data): string
    {
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        $db = Connection::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return json_encode(['error' => 'Invalid credentials']);
        }
        
        $secretKey = 'your-secret-key-change-in-production';
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'exp' => time() + 3600
        ];
        
        $token = JWT::encode($payload, $secretKey, 'HS256');
        
        return json_encode([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);
    }
}