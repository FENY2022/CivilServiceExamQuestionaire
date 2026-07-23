<?php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_email'])) {
    $email = strtolower(trim($_POST['delete_email']));
    $users = array_filter(get_users(), fn($user) => strtolower($user['email'] ?? '') !== $email);
    save_users($users);
    header('Location: admin.php');
    exit;
}

$users = get_users();
$confirmed = count(array_filter($users, fn($user) => !empty($user['confirmed'])));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="dashboard.php"><span>CSC</span>Admin Panel</a>
        <nav><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></nav>
    </header>
    <main class="container">
        <section class="stats-grid">
            <div class="stat-card"><strong><?= count($users) ?></strong><span>Total Users</span></div>
            <div class="stat-card"><strong><?= $confirmed ?></strong><span>Confirmed</span></div>
            <div class="stat-card"><strong><?= count($users) - $confirmed ?></strong><span>Pending</span></div>
        </section>
        <section class="table-card">
            <h1>Registered Users</h1>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Age</th><th>Email</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (!$users): ?>
                        <tr><td colspan="6">No registered users yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars((string)($user['age'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                            <td><span class="pill <?= !empty($user['confirmed']) ? 'ok' : 'pending' ?>"><?= !empty($user['confirmed']) ? 'Confirmed' : 'Pending' ?></span></td>
                            <td><?= htmlspecialchars(substr($user['created_at'] ?? '', 0, 10)) ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Delete this user?')">
                                    <input type="hidden" name="delete_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                    <button class="btn danger small" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
