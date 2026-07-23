<?php
declare(strict_types=1);

session_start();

define('APP_NAME', 'Philippines Civil Service Exam Reviewer');
define('APP_EMAIL', 'venzonanthonie@gmail.com');
define('APP_EMAIL_PASSWORD', 'irsw yeav xgqy rmll');
define('APP_EMAIL_FROM_NAME', 'Civil Service Reviewer');
define('ADMIN_USERNAME', 'feny');
define('ADMIN_PASSWORD', 'feny9959');

define('ROOT_PATH', __DIR__);
define('DATA_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'data');
define('USERS_FILE', DATA_PATH . DIRECTORY_SEPARATOR . 'users.json');

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

    foreach ([USERS_FILE] as $file) {
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
            chmod($file, 0664);
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
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    chmod($file, 0664);
}

function get_users(): array
{
    return read_json_file(USERS_FILE);
}

function save_users(array $users): void
{
    write_json_file(USERS_FILE, array_values($users));
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
    $isLoggedIn = $isAdmin || $isUser;
    $homeHref = $isLoggedIn ? 'dashboard.php' : 'index.php';
    $brandTitle = $title !== '' ? $title : APP_NAME;
    $links = [
        ['key' => 'home', 'label' => 'Home', 'href' => $homeHref],
        ['key' => 'about', 'label' => 'About', 'href' => 'about.php'],
    ];

    if ($isAdmin) {
        $links[] = ['key' => 'admin', 'label' => 'Admin Panel', 'href' => 'admin.php'];
        $links[] = ['key' => 'logout', 'label' => 'Logout', 'href' => 'logout.php'];
    } elseif ($isUser) {
        $links[] = ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard.php'];
        $links[] = ['key' => 'logout', 'label' => 'Logout', 'href' => 'logout.php'];
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
    if (empty($_SESSION['user']) && empty($_SESSION['admin'])) {
        header('Location: login.php');
        exit;
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
    return $_SESSION['user']['name'] ?? 'Reviewer';
}

ensure_storage();
