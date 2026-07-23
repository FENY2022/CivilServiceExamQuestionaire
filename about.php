<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/loader.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50 font-sans text-slate-900">
    <?php render_top_nav(APP_NAME, 'about'); ?>
    <main class="mx-auto w-full max-w-5xl px-4 py-8">
        <section class="rounded-[2rem] border border-blue-100 bg-white p-6 shadow-2xl sm:p-10">
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">About the System</p>
            <h1 class="mt-3 text-3xl font-black text-brand-950 sm:text-5xl">Civil Service Exam Reviewer</h1>
            <p class="mt-5 text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">This online reviewer system is designed to help aspiring civil servants prepare for the Career Service Examination through randomized practice quizzes, timed exams, and performance tracking.</p>
            <div class="mt-7 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                    <strong class="block text-2xl font-black text-brand-700">170</strong>
                    <span class="font-bold text-slate-600">Professional Questions</span>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                    <strong class="block text-2xl font-black text-brand-700">165</strong>
                    <span class="font-bold text-slate-600">Subprofessional Questions</span>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                    <strong class="block text-2xl font-black text-brand-700">80%</strong>
                    <span class="font-bold text-slate-600">Passing Benchmark</span>
                </div>
            </div>
            <div class="mt-7 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Programmed by</p>
                <p class="mt-1 text-xl font-black text-brand-950">ANTHONIE FENY V. CATALAN</p>
                <p class="text-sm font-semibold text-slate-500">IS Programmer</p>
            </div>
        </section>
    </main>
    <script src="js/loader.js"></script>
    <script src="js/toast.js"></script>
</body>
</html>
