<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $tries = self::credentialSets();
        $last  = null;

        foreach ($tries as $cfg) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $cfg['host'] ?? 'localhost',
                (int) ($cfg['port'] ?? 3306),
                $cfg['name'] ?? '',
                $cfg['charset'] ?? 'utf8mb4'
            );
            try {
                self::$pdo = new PDO(
                    $dsn,
                    (string) ($cfg['user'] ?? ''),
                    (string) ($cfg['pass'] ?? ''),
                    $opts
                );
                return self::$pdo;
            } catch (PDOException $e) {
                $last = $e;
            }
        }

        Response::error('Database connection failed. Check api/config/config.php.', 500, [
            'reference' => 'Check private database configuration',
        ]);
        return self::$pdo;
    }

    /**
     * Live GoDaddy account uses unprefixed names (confirmed working):
     * kitchenqueen_db / shaheerkhan
     */
    private static function credentialSets(): array { return [$GLOBALS['KQ_CONFIG']['db']]; }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $ph = array_map(static fn($c) => ':' . $c, $cols);
        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`,`', $cols),
            implode(',', $ph)
        );
        self::query($sql, $data);
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams): int
    {
        $sets = [];
        foreach (array_keys($data) as $c) {
            $sets[] = "`$c` = :$c";
        }
        $sql = sprintf('UPDATE `%s` SET %s WHERE %s', $table, implode(', ', $sets), $where);
        $st = self::query($sql, $data + $whereParams);
        return $st->rowCount();
    }
}
