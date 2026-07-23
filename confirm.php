<?php
require_once __DIR__ . '/config.php';

$token = trim($_GET['token'] ?? '');
$message = 'Invalid confirmation link.';
$status = 'error';

if ($token !== '') {
    $tokens = read_json_file(TOKENS_FILE);
    if (isset($tokens[$token])) {
        $entry = $tokens[$token];
        if (strtotime($entry['expires_at'] ?? '') < time()) {
            $message = 'This confirmation link has expired. Please register again.';
        } else {
            $users = get_users();
            foreach ($users as &$user) {
                if (strtolower($user['email'] ?? '') === strtolower($entry['email'] ?? '')) {
                    $user['confirmed'] = true;
                    $user['confirmed_at'] = date('c');
                    $_SESSION['user'] = $user;
                    $message = 'Your account is confirmed. You can now access the reviewer.';
                    $status = 'success';
                    break;
                }
            }
            unset($user);
            save_users($users);
            unset($tokens[$token]);
            write_json_file(TOKENS_FILE, $tokens);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="center-page">
    <main class="message-card">
        <div class="seal small">CSC</div>
        <h1>Registration Confirmation</h1>
        <div class="alert <?= htmlspecialchars($status) ?>"><?= htmlspecialchars($message) ?></div>
        <a class="btn primary" href="<?= $status === 'success' ? 'dashboard.php' : 'index.php' ?>"><?= $status === 'success' ? 'Go to Dashboard' : 'Back to Registration' ?></a>
    </main>
</body>
</html>
