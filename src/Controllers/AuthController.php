<?php

namespace TomOrder\Pos\Controllers;

use TomOrder\Pos\Database\Connection;

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
        
        $secretKey = $_ENV['JWT_SECRET'] ?? 'your-secret-key-change-in-production';
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'exp' => time() + 3600
        ];
        
        $token = $this->encodeJwt($payload, $secretKey);
        
        return json_encode([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);
    }

    private function encodeJwt(array $payload, string $secretKey): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $segments = [
            $this->base64UrlEncode((string) json_encode($header)),
            $this->base64UrlEncode((string) json_encode($payload)),
        ];

        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $secretKey, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
