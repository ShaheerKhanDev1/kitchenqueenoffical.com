<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function admin(): array
    {
        $token = Request::bearer();
        if (!$token) {
            Response::error('Admin login required', 401);
        }
        $row = Database::one(
            'SELECT s.id AS session_id, s.token, s.expires_at, a.*
             FROM admin_sessions s
             JOIN admins a ON a.id = s.admin_id
             WHERE s.token = :t LIMIT 1',
            ['t' => $token]
        );
        if (!$row || strtotime((string) $row['expires_at']) < time()) {
            Response::error('Admin session expired. Please login again.', 401);
        }
        if (!(int) $row['is_active']) {
            Response::error('Admin account is disabled', 403);
        }
        return $row;
    }

    public static function customer(?bool $required = true): ?array
    {
        $token = Request::bearer();
        if (!$token) {
            if ($required) {
                Response::error('Please sign in first', 401);
            }
            return null;
        }
        $user = self::parseCustomerToken($token);
        if (!$user && $required) {
            Response::error('Session expired. Please sign in again.', 401);
        }
        return $user;
    }

    public static function issueAdminSession(int $adminId): string
    {
        $token = bin2hex(random_bytes(32));
        $hours = (int) $GLOBALS['KQ_CONFIG']['session']['admin_hours'];
        Database::insert('admin_sessions', [
            'admin_id' => $adminId,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', time() + $hours * 3600),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $token;
    }

    public static function issueCustomerToken(array $user): string
    {
        $hours = (int) $GLOBALS['KQ_CONFIG']['session']['customer_hours'];
        $exp = time() + $hours * 3600;
        $payload = $user['id'] . '|' . $exp;
        $sig = hash_hmac('sha256', $payload, $GLOBALS['KQ_CONFIG']['app_secret']);
        return rtrim(strtr(base64_encode($payload . '|' . $sig), '+/', '-_'), '=');
    }

    public static function parseCustomerToken(string $token): ?array
    {
        $raw = base64_decode(strtr($token, '-_', '+/'), true);
        if (!$raw) {
            return null;
        }
        $parts = explode('|', $raw);
        if (count($parts) !== 3) {
            return null;
        }
        [$id, $exp, $sig] = $parts;
        $expect = hash_hmac('sha256', $id . '|' . $exp, $GLOBALS['KQ_CONFIG']['app_secret']);
        if (!hash_equals($expect, $sig) || (int) $exp < time()) {
            return null;
        }
        $user = Database::one('SELECT * FROM users WHERE id = :id AND is_active = 1', ['id' => (int) $id]);
        return $user ?: null;
    }
}
