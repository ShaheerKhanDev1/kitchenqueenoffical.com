<?php
declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function ok(array $data = [], string $message = 'OK'): never
    {
        self::json(['ok' => true, 'message' => $message, 'data' => $data], 200);
    }

    public static function created(array $data = [], string $message = 'Created'): never
    {
        self::json(['ok' => true, 'message' => $message, 'data' => $data], 201);
    }

    public static function error(string $message, int $status = 400, array $extra = []): never
    {
        self::json(['ok' => false, 'message' => $message, 'errors' => $extra], $status);
    }

    public static function cors(): void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed = $GLOBALS['KQ_CONFIG']['cors'] ?? [];
        if ($origin !== '') {
            if (!in_array($origin, $allowed, true) && !preg_match('#^http://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) self::error('Origin not allowed',403);
            header('Access-Control-Allow-Origin: '.$origin);header('Vary: Origin');
        }
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, X-Admin-Token, X-Customer-Token');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {http_response_code(204);exit;}
    }
}
