<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private static ?array $json = null;

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = str_replace('\\', '/', $path);
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base = rtrim(dirname($script), '/');
        if ($base && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }
        if (preg_match('#/api(?:/public)?(?:/index\.php)?(/.*)?$#i', $path, $m)) {
            $path = $m[1] ?? '/';
        }
        $path = preg_replace('#/index\.php$#', '', $path) ?: '/';
        $path = '/' . trim((string) $path, '/');
        return $path === '//' ? '/' : $path;
    }

    public static function json(): array
    {
        if (self::$json !== null) {
            return self::$json;
        }
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        self::$json = is_array($data) ? $data : [];
        return self::$json;
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        $json = self::json();
        if (array_key_exists($key, $json)) {
            return $json[$key];
        }
        if (array_key_exists($key, $_POST)) {
            return $_POST[$key];
        }
        if (array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }
        return $default;
    }

    public static function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return isset($_SERVER[$key]) ? (string) $_SERVER[$key] : null;
    }

    public static function bearer(): ?string
    {
        $h = self::header('Authorization') ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $h, $m)) {
            return trim($m[1]);
        }
        return self::header('X-Admin-Token') ?: self::header('X-Customer-Token');
    }

    public static function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
