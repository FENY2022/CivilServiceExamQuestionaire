<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/questions/question_bank.php';
require_login();

$type = strtolower($_GET['type'] ?? 'professional');
if (!in_array($type, ['professional', 'subprofessional'], true)) {
    $type = 'professional';
}

$exam = get_exam_questions($type);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= ucfirst($type) ?> Reviewer - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50 font-sans text-slate-900">
    <header class="sticky top-0 z-40 flex min-h-[74px] flex-col gap-3 border-b border-blue-100 bg-white/90 px-4 py-4 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:px-7">
        <a class="flex items-center gap-3 font-black text-brand-950" href="dashboard.php"><span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-700 text-white shadow-lg">CSC</span><?= ucfirst($type) ?> Reviewer</a>
        <nav class="flex gap-4 text-sm font-black text-brand-700"><a class="hover:text-brand-950" href="dashboard.php">Dashboard</a><a class="hover:text-brand-950" href="logout.php">Logout</a></nav>
    </header>
    <main class="mx-auto grid w-full max-w-[1440px] gap-5 px-4 py-5 lg:grid-cols-[320px_1fr]">
        <aside class="grid gap-4 self-start lg:sticky lg:top-24">
            <div class="rounded-3xl border border-blue-100 bg-white p-5 shadow-xl">
                <span class="text-sm font-extrabold text-slate-500">Time Remaining</span>
                <strong class="mt-1 block text-3xl font-black text-brand-700" id="timer">--:--:--</strong>
            </div>
            <div class="rounded-3xl border border-blue-100 bg-white p-5 shadow-xl">
                <span class="text-sm font-extrabold text-slate-500" id="progressText">0 answered</span>
                <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-200"><div class="h-full w-0 rounded-full bg-gradient-to-r from-brand-700 to-yellow-400 transition-all duration-300" id="progressBar"></div></div>
            </div>
            <nav id="categoryNav" class="custom-scrollbar max-h-[58vh] overflow-y-auto rounded-3xl border border-blue-100 bg-white p-3 shadow-xl"></nav>
        </aside>
        <section class="grid min-w-0 gap-5">
            <div class="rounded-3xl border border-blue-100 bg-white p-6 shadow-xl">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Civil Service Exam Practice</p>
                    <h1 class="mt-2 text-4xl font-black text-brand-950"><?= htmlspecialchars($exam['title']) ?></h1>
                </div>
            </div>
            <div id="quizArea"></div>
            <div id="resultArea" class="hidden rounded-3xl border border-blue-100 bg-white p-7 shadow-xl"></div>
        </section>
    </main>
    <button class="fixed bottom-5 right-5 z-50 rounded-full bg-gradient-to-r from-brand-950 to-brand-700 px-6 py-4 font-black text-white shadow-2xl shadow-blue-900/25 transition hover:-translate-y-1 hover:shadow-blue-700/40 sm:bottom-7 sm:right-7" id="submitQuiz" type="button">
        <span>Submit Exam</span>
        <span class="ml-2 rounded-full bg-white/20 px-2 py-1 text-xs" id="submitBadge">0 unanswered</span>
    </button>
    <div id="modalRoot"></div>
    <script>window.EXAM_DATA = <?= json_encode($exam, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
    <script src="js/toast.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
