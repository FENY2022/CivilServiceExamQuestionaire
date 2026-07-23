<?php
require_once __DIR__ . '/config.php';

$users = get_users();
$hasUsers = count($users) > 0;
$message = !$hasUsers ? 'No registered users yet. Please register first or use the admin account.' : '';
$status = !$hasUsers ? 'warning' : 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'user';
    if ($mode === 'admin') {
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
            $message = 'No account found with that email. Please register first.';
            $status = 'error';
        } elseif (empty($user['confirmed'])) {
            $message = 'Please confirm your email before logging in.';
            $status = 'warning';
        } else {
            $_SESSION['user'] = $user;
            unset($_SESSION['admin']);
            header('Location: dashboard.php');
            exit;
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page compact">
    <main class="auth-card standalone">
        <div class="seal small">CSC</div>
        <h1>Login</h1>
        <?php if ($message): ?><div class="alert <?= htmlspecialchars($status) ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <div class="tabs">
            <button class="tab active" type="button" data-tab="user">User</button>
            <button class="tab" type="button" data-tab="admin">Admin</button>
        </div>
        <form method="post" class="stack-form tab-panel active" id="user-panel">
            <input type="hidden" name="mode" value="user">
            <label>Email Address <input type="email" name="email" placeholder="yourname@gmail.com" required></label>
            <button class="btn primary" type="submit">Login to Reviewer</button>
        </form>
        <form method="post" class="stack-form tab-panel" id="admin-panel">
            <input type="hidden" name="mode" value="admin">
            <label>Admin Username <input type="text" name="username" value="admin" required></label>
            <label>Admin Password <input type="password" name="password" placeholder="admin123" required></label>
            <button class="btn primary" type="submit">Login as Admin</button>
        </form>
        <div class="auth-links"><a href="index.php">Create account</a></div>
    </main>
    <script>
    document.querySelectorAll('.tab').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.tab, .tab-panel').forEach(item => item.classList.remove('active'));
        button.classList.add('active');
        document.getElementById(button.dataset.tab + '-panel').classList.add('active');
    }));
    </script>
</body>
</html>
