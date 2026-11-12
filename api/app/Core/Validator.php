<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function require(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $f) {
            if (!isset($data[$f]) || $data[$f] === '' || $data[$f] === null) {
                $errors[$f] = 'Required';
            }
        }
        if ($errors) {
            Response::error('Validation failed', 422, $errors);
        }
        return $data;
    }

    public static function phone(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if (str_starts_with($d, '92') && strlen($d) === 12) {
            $d = '0' . substr($d, 2);
        }
        if (strlen($d) === 10 && str_starts_with($d, '3')) {
            $d = '0' . $d;
        }
        if (!preg_match('/^03\d{9}$/', $d)) {
            Response::error('Enter a valid Pakistani mobile number (03xxxxxxxxx)', 422);
        }
        return $d;
    }

    public static function e164(string $local03): string
    {
        return '92' . substr($local03, 1);
    }

    public static function email(?string $email): ?string
    {
        if ($email === null || $email === '') {
            return null;
        }
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email address', 422);
        }
        return $email;
    }

    public static function intRange(mixed $v, int $min, int $max, string $field): int
    {
        $n = (int) $v;
        if ($n < $min || $n > $max) {
            Response::error("$field is out of range", 422);
        }
        return $n;
    }

    public static function slug(string $name): string
    {
        $s = strtolower(trim($name));
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
        return trim($s, '-') ?: 'item';
    }
}
