<?php
require_once __DIR__ . '/config.php';
require_login();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50 font-sans text-slate-900">
    <header class="sticky top-0 z-40 flex min-h-[74px] flex-col gap-3 border-b border-blue-100 bg-white/90 px-4 py-4 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:px-7">
        <a class="flex items-center gap-3 font-black text-brand-950" href="dashboard.php"><span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-700 text-white shadow-lg">CSC</span><?= APP_NAME ?></a>
        <nav class="flex flex-wrap gap-4 text-sm font-black text-brand-700">
            <?php if (!empty($_SESSION['admin'])): ?><a class="hover:text-brand-950" href="admin.php">Admin Panel</a><?php endif; ?>
            <a class="hover:text-brand-950" href="logout.php">Logout</a>
        </nav>
    </header>
    <main class="mx-auto w-full max-w-6xl px-4 py-8">
        <section class="rounded-[2rem] border border-blue-100 bg-gradient-to-br from-white to-blue-50 p-8 shadow-2xl">
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Online Review Center</p>
            <h1 class="mt-3 text-4xl font-black text-brand-950">Welcome, <?= htmlspecialchars(current_user_name()) ?></h1>
            <p class="mt-3 max-w-3xl text-lg leading-8 text-slate-600">Select an exam type. Each reviewer uses randomized questions, a countdown timer, scoring, and the 80% passing benchmark.</p>
        </section>

        <section class="mt-7 grid gap-6 lg:grid-cols-2">
            <article class="relative overflow-hidden rounded-[2rem] border border-blue-100 bg-white p-8 shadow-2xl">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-blue-100"></div>
                <div class="relative grid h-16 w-16 place-items-center rounded-2xl bg-brand-700 text-2xl font-black text-white shadow-lg">P</div>
                <h2 class="relative mt-5 text-3xl font-black text-brand-950">Professional Level</h2>
                <p class="relative mt-3 text-slate-600">For second-level government positions. Includes general ability, general information, and advanced critical reasoning.</p>
                <ul class="relative mt-5 grid gap-2 text-slate-600">
                    <li><span class="font-black text-emerald-600">✓</span> 23 categories</li>
                    <li><span class="font-black text-emerald-600">✓</span> 170 randomized questions per exam</li>
                    <li><span class="font-black text-emerald-600">✓</span> Time limit: 3 hours 10 minutes</li>
                </ul>
                <a class="relative mt-7 inline-flex rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" href="reviewer.php?type=professional">Start Professional Reviewer</a>
            </article>
            <article class="relative overflow-hidden rounded-[2rem] border border-blue-100 bg-white p-8 shadow-2xl">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-slate-100"></div>
                <div class="relative grid h-16 w-16 place-items-center rounded-2xl bg-brand-950 text-2xl font-black text-white shadow-lg">S</div>
                <h2 class="relative mt-5 text-3xl font-black text-brand-950">Subprofessional Level</h2>
                <p class="relative mt-3 text-slate-600">For first-level government positions. Includes general ability, general information, and clerical operations.</p>
                <ul class="relative mt-5 grid gap-2 text-slate-600">
                    <li><span class="font-black text-emerald-600">✓</span> 24 categories</li>
                    <li><span class="font-black text-emerald-600">✓</span> 165 randomized questions per exam</li>
                    <li><span class="font-black text-emerald-600">✓</span> Time limit: 2 hours 40 minutes</li>
                </ul>
                <a class="relative mt-7 inline-flex rounded-2xl bg-gradient-to-r from-brand-950 to-brand-900 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" href="reviewer.php?type=subprofessional">Start Subprofessional Reviewer</a>
            </article>
        </section>
    </main>
    <script src="js/toast.js"></script>
</body>
</html>
