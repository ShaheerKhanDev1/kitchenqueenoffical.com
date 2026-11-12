<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class Settings
{
    public const PUBLIC_KEYS = [
        'store_online',
        'store_unavailable_message',
        'store_name',
        'easypaisa_name',
        'easypaisa_number',
        'contact_phone',
        'free_delivery_over',
        'tax_percent',
        'hours',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = Database::one('SELECT value FROM settings WHERE `key` = :k', ['k' => $key]);
        if (!$row) {
            return $default;
        }
        $v = $row['value'];
        if ($v === 'true' || $v === 'false') {
            return $v === 'true';
        }
        if (is_numeric($v)) {
            return str_contains((string) $v, '.') ? (float) $v : (int) $v;
        }
        $json = json_decode((string) $v, true);
        return json_last_error() === JSON_ERROR_NONE ? $json : $v;
    }

    public static function set(string $key, mixed $value): void
    {
        if (is_bool($value)) {
            $store = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $store = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $store = (string) $value;
        }
        $now = date('Y-m-d H:i:s');
        Database::query(
            'INSERT INTO settings (`key`, value, updated_at) VALUES (:k, :v, :u)
             ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = VALUES(updated_at)',
            ['k' => $key, 'v' => $store, 'u' => $now]
        );
    }

    public static function all(): array
    {
        $out = [];
        foreach (Database::all('SELECT `key`, value, updated_at FROM settings') as $row) {
            $out[$row['key']] = self::cast($row['value']);
        }
        return $out;
    }

    public static function public(): array
    {
        $all = self::all();
        $out = [];
        foreach (self::PUBLIC_KEYS as $k) {
            $out[$k] = $all[$k] ?? null;
        }
        return $out;
    }

    public static function storeOnline(): bool
    {
        $v = self::get('store_online', true);
        return $v === true || $v === 1 || $v === 'true' || $v === '1';
    }

    private static function cast(mixed $v): mixed
    {
        if ($v === 'true') {
            return true;
        }
        if ($v === 'false') {
            return false;
        }
        if (is_numeric($v)) {
            return str_contains((string) $v, '.') ? (float) $v : (int) $v;
        }
        $json = json_decode((string) $v, true);
        return json_last_error() === JSON_ERROR_NONE ? $json : $v;
    }
}
