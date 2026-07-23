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
            $message = 'Confirmation email sent to ' . $email . '. Please check your inbox or spam folder to confirm your account.';
            $status = 'success';
        } else {
            $message = 'Registration saved, but email sending failed for ' . $email . ': ' . $result['message'];
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
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/loader.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-sky-100 font-sans text-slate-900">
    <main class="mx-auto grid min-h-screen w-full max-w-6xl place-items-center gap-7 px-4 py-8 lg:grid-cols-[1.1fr_.9fr]">
        <section class="relative min-h-[520px] w-full overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 p-8 text-white shadow-2xl lg:p-12">
            <div class="absolute -bottom-24 -right-16 h-72 w-72 rounded-full border-[34px] border-yellow-400/30"></div>
            <div class="grid h-20 w-20 place-items-center rounded-full border-4 border-yellow-400 bg-gradient-to-br from-white to-blue-100 font-black tracking-tight text-brand-900 shadow-xl">CSC</div>
            <p class="mt-8 text-xs font-extrabold uppercase tracking-[.18em] text-blue-100">Republic of the Philippines</p>
            <h1 class="mt-3 max-w-xl text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">Civil Service Exam Reviewer</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-blue-50">Practice Professional and Subprofessional topics with timed quizzes, category tracking, and an 80% passing benchmark.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-bold backdrop-blur">Professional: 3h 10m</span>
                <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-bold backdrop-blur">Subprofessional: 2h 40m</span>
                <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-bold backdrop-blur">Passing: 80%</span>
            </div>
        </section>

        <section class="w-full rounded-[2rem] border border-blue-100 bg-white/95 p-7 shadow-2xl sm:p-9">
            <h2 class="text-3xl font-black text-brand-950">Create Reviewer Account</h2>
            <p class="mt-2 text-slate-600">Register first. A confirmation link will be sent to your Gmail account.</p>
            <form method="post" class="mt-7 grid gap-5">
                <label class="grid gap-2 font-extrabold text-brand-950">Name <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="name" required placeholder="Anthonie Feny V. Catalan"></label>
                <label class="grid gap-2 font-extrabold text-brand-950">Age <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="number" name="age" min="10" max="100" required placeholder="25"></label>
                <label class="grid gap-2 font-extrabold text-brand-950">Email Address <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="email" name="email" required placeholder="yourname@gmail.com"></label>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5 hover:shadow-blue-300/50">Register and Send Confirmation</button>
            </form>
            <div class="mt-6 flex flex-col justify-between gap-3 text-sm font-black text-brand-700 sm:flex-row">
                <a class="hover:text-brand-950" href="login.php">Already confirmed? Login</a>
                <a class="hover:text-brand-950" href="login.php?admin=1">Admin Login</a>
            </div>
        </section>
    </main>
    <script src="js/loader.js"></script>
    <script src="js/toast.js"></script>
    <?php if ($message): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($message) ?>, <?= json_encode($status) ?>));</script>
    <?php endif; ?>
</body>
</html>
