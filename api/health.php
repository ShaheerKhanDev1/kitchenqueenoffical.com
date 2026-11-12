<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
$cfg = require __DIR__ . '/config/config.php';

$candidates = [];
$add = static function (array $row) use (&$candidates): void {
    $host = (string) ($row['host'] ?? 'localhost');
    $name = (string) ($row['name'] ?? $row['database'] ?? '');
    $user = (string) ($row['user'] ?? $row['username'] ?? '');
    $pass = (string) ($row['pass'] ?? $row['password'] ?? '');
    $port = (int) ($row['port'] ?? 3306);
    $key = "$host|$port|$name|$user";
    if ($name === '' || $user === '' || isset($candidates[$key])) {
        return;
    }
    $candidates[$key] = compact('host', 'port', 'name', 'user', 'pass');
};

if (!empty($cfg['db'])) $add($cfg['db']);
if (!empty($cfg['db_fallback'])) $add($cfg['db_fallback']);

$out = [
    'ok' => false,
    'php' => PHP_VERSION,
    'db_name' => $cfg['db']['name'] ?? '',
    'db_user' => $cfg['db']['user'] ?? '',
    'db_connected' => false,
    'db_error' => null,
];

foreach ($candidates as $try) {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $try['host'],
            $try['port'],
            $try['name']
        );
        $pdo = new PDO($dsn, $try['user'], $try['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $out['ok'] = true;
        $out['db_connected'] = true;
        $out['db_name'] = $try['name'];
        $out['db_user'] = $try['user'];
        $out['db_host'] = $try['host'];
        $out['tables'] = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $out['db_error'] = null;
        echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Throwable $e) {
        $out['db_error'] = $e->getMessage();
    }
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
