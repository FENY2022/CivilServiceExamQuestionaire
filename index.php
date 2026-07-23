<?php
require_once __DIR__ . '/config.php';

$message = '';
$status = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $age = trim($_POST['age'] ?? '');
    $captcha = (int)($_POST['captcha'] ?? 0);
    $expectedCaptcha = (int)($_SESSION['captcha_answer'] ?? -1);

    if ($name === '') {
        $message = 'Please enter your full name.';
        $status = 'error';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $status = 'error';
    } elseif ($age === '' || !ctype_digit($age) || (int)$age < 1) {
        $message = 'Please enter a valid age.';
        $status = 'error';
    } elseif ($captcha !== $expectedCaptcha) {
        $message = 'Incorrect CAPTCHA answer. Please solve the math question again.';
        $status = 'error';
    } elseif (find_user_by_email($email)) {
        $message = 'This email address is already registered. Please login or wait for admin approval.';
        $status = 'warning';
    } else {
        $users = get_users();
        $userId = bin2hex(random_bytes(8));
        $now = date('c');

        $users[] = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'age' => (int)$age,
            'status' => 'pending',
            'created_at' => $now,
            'confirmed_at' => null,
            'disabled_at' => null,
        ];
        save_users($users);

        $message = 'Registration successful. Please wait for admin approval before logging in.';
        $status = 'success';
    }
}

$captchaA = random_int(1, 20);
$captchaB = random_int(1, 20);
$_SESSION['captcha_answer'] = $captchaA + $captchaB;
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
    <?php render_top_nav('Civil Service Exam Reviewer', 'register'); ?>
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
            <p class="mt-2 text-slate-600">Register your details first. Admin approval is required before you can access the reviewer.</p>
            <form method="post" class="mt-7 grid gap-5">
                <label class="grid gap-2 font-extrabold text-brand-950">Full Name <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="name" required placeholder="Anthonie Feny V. Catalan"></label>
                <label class="grid gap-2 font-extrabold text-brand-950">Email Address <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="email" name="email" required placeholder="you@example.com"></label>
                <label class="grid gap-2 font-extrabold text-brand-950">Age <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="number" name="age" min="1" required placeholder="Enter your age"></label>
                <label class="grid gap-2 font-extrabold text-brand-950">Math CAPTCHA <span class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-slate-700">What is <?= $captchaA ?> + <?= $captchaB ?>?</span><input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="number" name="captcha" required placeholder="Enter answer"></label>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5 hover:shadow-blue-300/50">Register</button>
            </form>
            <div class="mt-6 flex flex-col justify-between gap-3 text-sm font-black text-brand-700 sm:flex-row">
                <a class="hover:text-brand-950" href="login.php">Already approved? Login</a>
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
