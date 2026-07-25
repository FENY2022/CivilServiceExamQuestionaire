<?php
declare(strict_types=1);

class DB
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dbDir = defined('ROOT_PATH') ? ROOT_PATH . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'data' : __DIR__ . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'data';

        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0775, true);
        }

        $dbPath = $dbDir . DIRECTORY_SEPARATOR . 'app.db';
        $dsn = 'sqlite:' . $dbPath;

        self::$pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$pdo->exec('PRAGMA journal_mode=WAL');
        self::$pdo->exec('PRAGMA foreign_keys=ON');

        self::migrate();
        self::importLegacyJson($dbDir);

        return self::$pdo;
    }

    private static function migrate(): void
    {
        self::$pdo->exec('
            CREATE TABLE IF NOT EXISTS users (
                id TEXT PRIMARY KEY,
                name TEXT NOT NULL DEFAULT \'\',
                email TEXT NOT NULL DEFAULT \'\',
                age INTEGER NOT NULL DEFAULT 0,
                status TEXT NOT NULL DEFAULT \'pending\',
                created_at TEXT NOT NULL DEFAULT \'\',
                confirmed_at TEXT,
                disabled_at TEXT
            )
        ');

        self::$pdo->exec('
            CREATE TABLE IF NOT EXISTS settings (
                key TEXT PRIMARY KEY,
                value TEXT NOT NULL DEFAULT \'\'
            )
        ');

        self::$pdo->exec('
            CREATE TABLE IF NOT EXISTS guest_logs (
                id TEXT PRIMARY KEY,
                nickname TEXT NOT NULL DEFAULT \'\',
                accessed_at TEXT NOT NULL DEFAULT \'\'
            )
        ');
    }

    private static function importLegacyJson(string $dbDir): void
    {
        $projectRoot = defined('ROOT_PATH') ? ROOT_PATH : __DIR__;
        $legacyDir = $projectRoot . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
        $tables = [
            'users' => [
                'file' => 'users.json',
                'columns' => ['id', 'name', 'email', 'age', 'status', 'created_at', 'confirmed_at', 'disabled_at'],
            ],
            'guest_logs' => [
                'file' => 'guest_logs.json',
                'columns' => ['id', 'nickname', 'accessed_at'],
            ],
        ];

        foreach ($tables as $table => $info) {
            $count = (int) self::$pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
            if ($count > 0) {
                continue;
            }

            $filePath = $legacyDir . $info['file'];
            if (!file_exists($filePath)) {
                continue;
            }

            $raw = file_get_contents($filePath);
            $data = json_decode($raw ?: '[]', true);
            if (!is_array($data) || empty($data)) {
                continue;
            }

            $placeholders = implode(', ', array_fill(0, count($info['columns']), '?'));
            $cols = implode(', ', $info['columns']);
            $stmt = self::$pdo->prepare("INSERT INTO {$table} ({$cols}) VALUES ({$placeholders})");

            foreach ($data as $row) {
                $values = [];
                foreach ($info['columns'] as $col) {
                    $val = $row[$col] ?? null;
                    if ($table === 'users' && $col === 'status' && $val === null) {
                        $val = !empty($row['confirmed']) ? 'confirmed' : 'pending';
                    }
                    $values[] = $val;
                }
                $stmt->execute($values);
            }
        }

        $settingsFile = $legacyDir . 'settings.json';
        if (file_exists($settingsFile)) {
            $count = (int) self::$pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
            if ($count === 0) {
                $raw = file_get_contents($settingsFile);
                $data = json_decode($raw ?: '{}', true);
                if (is_array($data)) {
                    $stmt = self::$pdo->prepare('INSERT INTO settings (key, value) VALUES (?, ?)');
                    foreach ($data as $key => $value) {
                        $stmt->execute([$key, is_bool($value) ? ($value ? '1' : '0') : (string) $value]);
                    }
                }
            }
        }
    }
}
