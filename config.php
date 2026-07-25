<?php
declare(strict_types=1);

session_start();

define('ROOT_PATH', __DIR__);
define('DATA_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'data');

require_once ROOT_PATH . '/db.php';

define('APP_NAME', 'Philippines Civil Service Exam Reviewer');
define('APP_EMAIL', $_ENV['APP_EMAIL'] ?? 'venzonanthonie@gmail.com');
define('APP_EMAIL_PASSWORD', $_ENV['APP_EMAIL_PASSWORD'] ?? 'irsw yeav xgqy rmll');
define('APP_EMAIL_FROM_NAME', 'Civil Service Reviewer');
define('ADMIN_USERNAME', $_ENV['ADMIN_USERNAME'] ?? 'feny');
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD'] ?? 'feny9959');

function app_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $scheme . '://' . $host . ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function get_users(): array
{
    $pdo = DB::connect();
    $rows = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    return array_map(function ($row) {
        $row['age'] = (int) $row['age'];
        return $row;
    }, $rows);
}

function save_users(array $users): void
{
    $pdo = DB::connect();
    $pdo->exec('DELETE FROM users');
    $stmt = $pdo->prepare('INSERT INTO users (id, name, email, age, status, created_at, confirmed_at, disabled_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($users as $user) {
        $stmt->execute([
            $user['id'] ?? '',
            $user['name'] ?? '',
            $user['email'] ?? '',
            (int) ($user['age'] ?? 0),
            $user['status'] ?? 'pending',
            $user['created_at'] ?? '',
            $user['confirmed_at'] ?? null,
            $user['disabled_at'] ?? null,
        ]);
    }
}

function get_settings(): array
{
    $pdo = DB::connect();
    $rows = $pdo->query('SELECT key, value FROM settings')->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }
    return array_merge(['guest_mode' => false], $settings);
}

function save_settings(array $settings): void
{
    $current = get_settings();
    $merged = array_merge($current, $settings);
    $pdo = DB::connect();
    $pdo->exec('DELETE FROM settings');
    $stmt = $pdo->prepare('INSERT INTO settings (key, value) VALUES (?, ?)');
    foreach ($merged as $key => $value) {
        $stmt->execute([$key, is_bool($value) ? ($value ? '1' : '0') : (string) $value]);
    }
}

function is_guest_mode(): bool
{
    $val = get_settings()['guest_mode'] ?? false;
    return $val === true || $val === '1' || $val === 1;
}

function get_guest_logs(): array
{
    $pdo = DB::connect();
    return $pdo->query('SELECT * FROM guest_logs ORDER BY accessed_at DESC')->fetchAll();
}

function log_guest_access(string $nickname): void
{
    $pdo = DB::connect();
    $stmt = $pdo->prepare('INSERT INTO guest_logs (id, nickname, accessed_at) VALUES (?, ?, ?)');
    $stmt->execute([bin2hex(random_bytes(8)), $nickname, date('c')]);
}

function find_user_by_name(string $name): ?array
{
    $name = strtolower(trim($name));
    foreach (get_users() as $user) {
        if (strtolower(trim($user['name'] ?? '')) === $name) {
            return $user;
        }
    }
    return null;
}

function find_user_by_email(string $email): ?array
{
    $email = strtolower(trim($email));
    foreach (get_users() as $user) {
        if (strtolower(trim($user['email'] ?? '')) === $email) {
            return $user;
        }
    }
    return null;
}

function render_top_nav(string $title = '', string $active = '', string $extraHtml = ''): void
{
    $isAdmin = !empty($_SESSION['admin']);
    $isUser = !empty($_SESSION['user']);
    $isGuest = !empty($_SESSION['guest']);
    $isLoggedIn = $isAdmin || $isUser || $isGuest;
    $homeHref = $isLoggedIn ? 'dashboard.php' : 'index.php';
    $brandTitle = $title !== '' ? $title : APP_NAME;
    $links = [
        ['key' => 'home', 'label' => 'Home', 'href' => $homeHref],
        ['key' => 'about', 'label' => 'About', 'href' => 'about.php'],
    ];

    if ($isAdmin) {
        $links[] = ['key' => 'admin', 'label' => 'Admin Panel', 'href' => 'admin.php'];
        $links[] = ['key' => 'logout', 'label' => 'Logout', 'href' => 'logout.php'];
    } elseif ($isUser || $isGuest) {
        $links[] = ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard.php'];
        $links[] = ['key' => 'logout', 'label' => 'Logout', 'href' => 'logout.php'];
    } elseif (is_guest_mode()) {
        $links[] = ['key' => 'guest', 'label' => 'Guest Access', 'href' => 'login.php?guest=1'];
        $links[] = ['key' => 'login', 'label' => 'Admin Login', 'href' => 'login.php?admin=1'];
    } else {
        $links[] = ['key' => 'login', 'label' => 'Login', 'href' => 'login.php'];
        $links[] = ['key' => 'register', 'label' => 'Register', 'href' => 'index.php'];
    }
    ?>
    <header class="sticky top-0 z-40 flex min-h-[74px] flex-col gap-3 border-b border-blue-100 bg-white/90 px-4 py-4 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:px-7">
        <a class="flex items-center gap-3 font-black text-brand-950" href="<?= htmlspecialchars($homeHref) ?>"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-700 text-white shadow-lg">CSC</span><span class="hidden sm:block"><?= htmlspecialchars($brandTitle) ?></span></a>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <?= $extraHtml ?>
            <nav class="flex flex-wrap gap-4 text-sm font-black text-brand-700">
                <?php foreach ($links as $link): ?>
                    <a class="<?= $active === $link['key'] ? 'text-brand-950 underline decoration-yellow-400 decoration-4 underline-offset-8' : 'hover:text-brand-950' ?>" href="<?= htmlspecialchars($link['href']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>
    <?php
}

function require_login(): void
{
    if (empty($_SESSION['user']) && empty($_SESSION['admin']) && empty($_SESSION['guest'])) {
        header('Location: login.php');
        exit;
    }

    if (!empty($_SESSION['guest'])) {
        if (!is_guest_mode()) {
            unset($_SESSION['guest']);
            header('Location: login.php');
            exit;
        }
        return;
    }

    if (!empty($_SESSION['user'])) {
        $sessionUser = $_SESSION['user'];
        $currentUser = null;
        foreach (get_users() as $user) {
            if (($user['id'] ?? '') === ($sessionUser['id'] ?? '')) {
                $currentUser = $user;
                break;
            }
        }

        $accountStatus = $currentUser['status'] ?? (!empty($currentUser['confirmed']) ? 'confirmed' : 'pending');
        if (!$currentUser || $accountStatus !== 'confirmed') {
            unset($_SESSION['user']);
            header('Location: login.php');
            exit;
        }

        $_SESSION['user'] = $currentUser;
    }
}

function current_user_name(): string
{
    if (!empty($_SESSION['admin'])) {
        return 'Administrator';
    }
    if (!empty($_SESSION['guest'])) {
        return $_SESSION['guest']['nickname'] ?? 'Guest';
    }
    return $_SESSION['user']['name'] ?? 'Reviewer';
}
