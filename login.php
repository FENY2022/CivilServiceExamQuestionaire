<?php
require_once __DIR__ . '/config.php';

$users = get_users();
$hasUsers = count($users) > 0;
$message = !$hasUsers ? 'No registered users yet. Please register first or use the admin account.' : '';
$status = !$hasUsers ? 'warning' : 'info';
$defaultTab = !empty($_GET['admin']) ? 'admin' : (!empty($_GET['guest']) ? 'guest' : 'user');
$guestMode = is_guest_mode();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'user';
    if ($mode === 'guest') {
        $defaultTab = 'guest';
        $nickname = trim($_POST['nickname'] ?? '');
        if (!is_guest_mode()) {
            $message = 'Guest access is currently disabled.';
            $status = 'error';
        } elseif ($nickname === '') {
            $message = 'Please enter your nickname.';
            $status = 'error';
            $defaultTab = 'guest';
        } else {
            log_guest_access($nickname);
            $_SESSION['guest'] = [
                'nickname' => $nickname,
                'started_at' => date('c'),
            ];
            unset($_SESSION['user'], $_SESSION['admin']);
            header('Location: dashboard.php');
            exit;
        }
    } elseif ($mode === 'admin') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin'] = true;
            unset($_SESSION['user']);
            header('Location: admin.php');
            exit;
        }
        $message = 'Invalid admin credentials.';
        $status = 'error';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $user = find_user_by_email($email);
        if (!$user) {
            $message = 'No account found with that email address. Please register first.';
            $status = 'error';
        } else {
            $accountStatus = $user['status'] ?? (!empty($user['confirmed']) ? 'confirmed' : 'pending');
            if ($accountStatus === 'pending') {
                $message = 'Your account is pending admin approval.';
                $status = 'warning';
            } elseif ($accountStatus === 'disabled') {
                $message = 'Your account has been disabled. Please contact the admin.';
                $status = 'error';
            } else {
                $_SESSION['user'] = $user;
                unset($_SESSION['admin']);
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> - Login</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/loader.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-sky-100 font-sans text-slate-900">
    <?php render_top_nav('Civil Service Exam Reviewer', 'login'); ?>
    <main class="mx-auto grid min-h-[calc(100vh-74px)] w-full max-w-md place-items-center px-4 py-8">
    <section class="w-full rounded-[2rem] border border-blue-100 bg-white/95 p-7 shadow-2xl sm:p-9">
        <div class="mb-4 grid h-16 w-16 place-items-center rounded-full border-4 border-yellow-400 bg-gradient-to-br from-white to-blue-100 font-black text-brand-900 shadow-lg">CSC</div>
        <h1 class="text-4xl font-black text-brand-950">Login</h1>
        <div class="mt-6 grid grid-cols-3 gap-2 rounded-2xl bg-blue-50 p-2">
            <button class="tab rounded-xl py-3 font-black text-brand-900 transition hover:bg-white/60 <?= $defaultTab === 'user' ? 'bg-white shadow-md' : '' ?>" type="button" data-tab="user">User</button>
            <button class="tab rounded-xl py-3 font-black text-brand-900 transition hover:bg-white/60 <?= $defaultTab === 'guest' ? 'bg-white shadow-md' : '' ?>" type="button" data-tab="guest">Guest</button>
            <button class="tab rounded-xl py-3 font-black text-brand-900 transition hover:bg-white/60 <?= $defaultTab === 'admin' ? 'bg-white shadow-md' : '' ?>" type="button" data-tab="admin">Admin</button>
        </div>
        <form method="post" class="tab-panel mt-6 grid <?= $defaultTab === 'user' ? '' : 'hidden' ?> gap-5" id="user-panel">
            <input type="hidden" name="mode" value="user">
            <label class="grid gap-2 font-extrabold text-brand-950">Email Address <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="email" name="email" placeholder="Enter your registered email" required></label>
            <button class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" type="submit">Login to Reviewer</button>
        </form>
        <form method="post" class="tab-panel mt-6 grid <?= $defaultTab === 'guest' ? '' : 'hidden' ?> gap-5" id="guest-panel">
            <input type="hidden" name="mode" value="guest">
            <div class="rounded-2xl border <?= $guestMode ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-700' ?> p-4 text-sm font-bold leading-6">
                <?= $guestMode ? 'Guest access is open. Enter any nickname to use the reviewer.' : 'Guest access is currently disabled by the admin.' ?>
            </div>
            <label class="grid gap-2 font-extrabold text-brand-950">Nickname <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="nickname" maxlength="60" placeholder="Enter your nickname" required></label>
            <button class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60" type="submit" <?= $guestMode ? '' : 'disabled' ?>>Enter as Guest</button>
        </form>
        <form method="post" class="tab-panel mt-6 grid <?= $defaultTab === 'admin' ? '' : 'hidden' ?> gap-5" id="admin-panel">
            <input type="hidden" name="mode" value="admin">
            <label class="grid gap-2 font-extrabold text-brand-950">Admin Username <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="username" value="feny" required></label>
            <label class="grid gap-2 font-extrabold text-brand-950">Admin Password <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="password" name="password" required></label>
            <button class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" type="submit">Login as Admin</button>
        </form>
        <div class="mt-6 font-black text-brand-700"><a class="hover:text-brand-950" href="index.php">Create account</a></div>
    </section>
    </main>
    <script src="js/loader.js"></script>
    <script src="js/toast.js"></script>
    <script>
    document.querySelectorAll('.tab').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(item => item.classList.remove('bg-white', 'shadow-md'));
        document.querySelectorAll('.tab-panel').forEach(item => item.classList.add('hidden'));
        button.classList.add('bg-white', 'shadow-md');
        document.getElementById(button.dataset.tab + '-panel').classList.remove('hidden');
    }));
    </script>
    <?php if ($message): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($message) ?>, <?= json_encode($status) ?>));</script>
    <?php endif; ?>
</body>
</html>
