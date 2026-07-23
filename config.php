<?php
declare(strict_types=1);

session_start();

define('APP_NAME', 'Philippines Civil Service Exam Reviewer');
define('APP_EMAIL', 'venzonanthonie@gmail.com');
define('APP_EMAIL_PASSWORD', 'irsw yeav xgqy rmll');
define('APP_EMAIL_FROM_NAME', 'Civil Service Reviewer');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

define('ROOT_PATH', __DIR__);
define('DATA_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'data');
define('USERS_FILE', DATA_PATH . DIRECTORY_SEPARATOR . 'users.json');
define('TOKENS_FILE', DATA_PATH . DIRECTORY_SEPARATOR . 'tokens.json');

function app_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $scheme . '://' . $host . ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function ensure_storage(): void
{
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0775, true);
    }

    foreach ([USERS_FILE, TOKENS_FILE] as $file) {
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([], JSON_PRETTY_PRINT), 0664);
        }
    }
}

function read_json_file(string $file): array
{
    ensure_storage();
    $content = file_get_contents($file);
    $data = json_decode($content ?: '[]', true);
    return is_array($data) ? $data : [];
}

function write_json_file(string $file, array $data): void
{
    ensure_storage();
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX | 0664);
}

function get_users(): array
{
    return read_json_file(USERS_FILE);
}

function save_users(array $users): void
{
    write_json_file(USERS_FILE, array_values($users));
}

function find_user_by_email(string $email): ?array
{
    $email = strtolower(trim($email));
    foreach (get_users() as $user) {
        if (strtolower($user['email'] ?? '') === $email) {
            return $user;
        }
    }
    return null;
}

function require_login(): void
{
    if (empty($_SESSION['user']) && empty($_SESSION['admin'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_name(): string
{
    if (!empty($_SESSION['admin'])) {
        return 'Administrator';
    }
    return $_SESSION['user']['name'] ?? 'Reviewer';
}

ensure_storage();
