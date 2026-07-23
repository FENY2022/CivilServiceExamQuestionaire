<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/sendemail/mailer.php';

$message = '';
$status = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $email = strtolower(trim($_POST['email'] ?? ''));

    if ($name === '' || $age < 10 || $age > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid name, age, and email address.';
        $status = 'error';
    } elseif (find_user_by_email($email)) {
        $message = 'This email is already registered. Please login or check your confirmation email.';
        $status = 'warning';
    } else {
        $users = get_users();
        $userId = bin2hex(random_bytes(8));
        $token = bin2hex(random_bytes(24));
        $now = date('c');

        $users[] = [
            'id' => $userId,
            'name' => $name,
            'age' => $age,
            'email' => $email,
            'confirmed' => false,
            'created_at' => $now,
            'confirmed_at' => null,
        ];
        save_users($users);

        $tokens = read_json_file(TOKENS_FILE);
        $tokens[$token] = [
            'email' => $email,
            'created_at' => $now,
            'expires_at' => date('c', time() + 86400),
        ];
        write_json_file(TOKENS_FILE, $tokens);

        $result = send_confirmation_email($email, $name, app_url('confirm.php?token=' . urlencode($token)));
        if ($result['ok']) {
            $message = 'Registration successful. Please check your Gmail inbox or spam folder to confirm your account.';
            $status = 'success';
        } else {
            $message = 'Registration saved, but email sending failed: ' . $result['message'];
            $status = 'warning';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="hero-panel">
            <div class="seal">CSC</div>
            <p class="eyebrow">Republic of the Philippines</p>
            <h1>Civil Service Exam Reviewer</h1>
            <p>Practice Professional and Subprofessional topics with timed quizzes, category tracking, and an 80% passing benchmark.</p>
            <div class="hero-stats">
                <span>Professional: 3h 10m</span>
                <span>Subprofessional: 2h 40m</span>
                <span>Passing: 80%</span>
            </div>
        </section>

        <section class="auth-card">
            <h2>Create Reviewer Account</h2>
            <p class="muted">Register first. A confirmation link will be sent to your Gmail account.</p>
            <?php if ($message): ?><div class="alert <?= htmlspecialchars($status) ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <form method="post" class="stack-form">
                <label>Name <input type="text" name="name" required placeholder="Juan Dela Cruz"></label>
                <label>Age <input type="number" name="age" min="10" max="100" required placeholder="25"></label>
                <label>Email Address <input type="email" name="email" required placeholder="yourname@gmail.com"></label>
                <button type="submit" class="btn primary">Register and Send Confirmation</button>
            </form>
            <div class="auth-links">
                <a href="login.php">Already confirmed? Login</a>
                <a href="login.php?admin=1">Admin Login</a>
            </div>
        </section>
    </main>
</body>
</html>
