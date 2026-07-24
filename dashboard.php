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
    <link rel="stylesheet" href="css/loader.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50 font-sans text-slate-900">
    <?php render_top_nav(APP_NAME, 'dashboard'); ?>
    <main class="mx-auto w-full max-w-6xl px-4 py-8">
        <section class="rounded-[2rem] border border-blue-100 bg-gradient-to-br from-white to-blue-50 p-5 shadow-2xl sm:p-8">
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Online Review Center</p>
            <h1 class="mt-3 break-words text-2xl font-black text-brand-950 sm:text-3xl lg:text-4xl">Welcome, <?= htmlspecialchars(current_user_name()) ?></h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">Select an exam type. Each reviewer uses randomized questions, a countdown timer, scoring, and the 80% passing benchmark.</p>
        </section>

        <section class="mt-7 grid gap-6 lg:grid-cols-2">
            <article class="relative overflow-hidden rounded-[2rem] border border-blue-100 bg-white p-5 shadow-2xl sm:p-8">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-blue-100"></div>
                <div class="relative grid h-16 w-16 place-items-center rounded-2xl bg-brand-700 text-2xl font-black text-white shadow-lg">P</div>
                <h2 class="relative mt-5 text-2xl font-black text-brand-950 sm:text-3xl">Professional Level</h2>
                <p class="relative mt-3 text-slate-600">For second-level government positions. Includes general ability, general information, and advanced critical reasoning.</p>
                <ul class="relative mt-5 grid gap-2 text-slate-600">
                    <li><span class="font-black text-emerald-600">✓</span> 23 categories</li>
                    <li><span class="font-black text-emerald-600">✓</span> 170 randomized questions per exam</li>
                    <li><span class="font-black text-emerald-600">✓</span> Time limit: 3 hours 10 minutes</li>
                </ul>
                <div class="relative mt-7 grid gap-3 sm:grid-cols-2">
                    <a class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 text-center font-black text-white shadow-xl transition hover:-translate-y-0.5" href="reviewer.php?type=professional&amp;timer=1">Start with Timer</a>
                    <a class="inline-flex w-full justify-center rounded-2xl border border-blue-200 bg-white px-5 py-4 text-center font-black text-brand-700 shadow-lg transition hover:-translate-y-0.5 hover:bg-blue-50" href="reviewer.php?type=professional&amp;timer=0">Start without Timer</a>
                </div>
            </article>
            <article class="relative overflow-hidden rounded-[2rem] border border-blue-100 bg-white p-5 shadow-2xl sm:p-8">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-slate-100"></div>
                <div class="relative grid h-16 w-16 place-items-center rounded-2xl bg-brand-950 text-2xl font-black text-white shadow-lg">S</div>
                <h2 class="relative mt-5 text-2xl font-black text-brand-950 sm:text-3xl">Subprofessional Level</h2>
                <p class="relative mt-3 text-slate-600">For first-level government positions. Includes general ability, general information, and clerical operations.</p>
                <ul class="relative mt-5 grid gap-2 text-slate-600">
                    <li><span class="font-black text-emerald-600">✓</span> 24 categories</li>
                    <li><span class="font-black text-emerald-600">✓</span> 165 randomized questions per exam</li>
                    <li><span class="font-black text-emerald-600">✓</span> Time limit: 2 hours 40 minutes</li>
                </ul>
                <div class="relative mt-7 grid gap-3 sm:grid-cols-2">
                    <a class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-brand-950 to-brand-900 px-5 py-4 text-center font-black text-white shadow-xl transition hover:-translate-y-0.5" href="reviewer.php?type=subprofessional&amp;timer=1">Start with Timer</a>
                    <a class="inline-flex w-full justify-center rounded-2xl border border-blue-200 bg-white px-5 py-4 text-center font-black text-brand-700 shadow-lg transition hover:-translate-y-0.5 hover:bg-blue-50" href="reviewer.php?type=subprofessional&amp;timer=0">Start without Timer</a>
                </div>
            </article>
        </section>

        <section class="mt-7 rounded-[2rem] border border-blue-100 bg-white p-5 shadow-2xl sm:p-8" id="sampleQuiz">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Demo Questionnaire</p>
                    <h2 class="mt-3 text-2xl font-black text-brand-950 sm:text-3xl">Try a Sample Quiz</h2>
                    <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Answer 5 sample civil service questions and get your score instantly.</p>
                </div>
                <div class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-black text-brand-700">5 questions</div>
            </div>

            <div class="mt-6 grid gap-4" id="demoQuiz">
                <article class="demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="1">
                    <h3 class="text-lg font-black leading-7 text-brand-950">1. Choose the correct sentence.</h3>
                    <div class="mt-4 grid gap-2">
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo1" value="0">A. She do her work carefully.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo1" value="1">B. She does her work carefully.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo1" value="2">C. She doing her work carefully.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo1" value="3">D. She done her work carefully.</label>
                    </div>
                    <p class="demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> Singular subject "she" uses "does".</p>
                </article>

                <article class="demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="0">
                    <h3 class="text-lg font-black leading-7 text-brand-950">2. PANACEA most nearly means:</h3>
                    <div class="mt-4 grid gap-2">
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo2" value="0">A. cure-all</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo2" value="1">B. conflict</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo2" value="2">C. standard</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo2" value="3">D. warning</label>
                    </div>
                    <p class="demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> A panacea is a remedy for all problems.</p>
                </article>

                <article class="demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="1">
                    <h3 class="text-lg font-black leading-7 text-brand-950">3. What is 4 x 8 + 12 / 4 - 8 / 2?</h3>
                    <div class="mt-4 grid gap-2">
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo3" value="0">A. 30</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo3" value="1">B. 31</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo3" value="2">C. 35</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo3" value="3">D. 39</label>
                    </div>
                    <p class="demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> Apply order of operations: 32 + 3 - 4 = 31.</p>
                </article>

                <article class="demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="0">
                    <h3 class="text-lg font-black leading-7 text-brand-950">4. All luxuries are needless expenditures. Cable TV is a luxury. Therefore:</h3>
                    <div class="mt-4 grid gap-2">
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo4" value="0">A. Cable TV is needless expenditure.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo4" value="1">B. All expenditures are luxuries.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo4" value="2">C. Cable TV is free.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo4" value="3">D. No conclusion.</label>
                    </div>
                    <p class="demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> This follows directly by syllogism.</p>
                </article>

                <article class="demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="0">
                    <h3 class="text-lg font-black leading-7 text-brand-950">5. A passage says pollutants cross national borders through wind. What is the best conclusion?</h3>
                    <div class="mt-4 grid gap-2">
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo5" value="0">A. Pollution can affect distant places.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo5" value="1">B. Pollution stays in one country.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo5" value="2">C. Wind prevents pollution.</label>
                        <label class="demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="demo5" value="3">D. Only cities are polluted.</label>
                    </div>
                    <p class="demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> The main idea is that pollutants can travel far.</p>
                </article>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" id="submitDemoQuiz" type="button">Submit Sample Quiz</button>
                <button class="hidden rounded-2xl border border-blue-200 bg-white px-5 py-4 font-black text-brand-700 transition hover:bg-blue-50" id="resetDemoQuiz" type="button">Try Again</button>
            </div>
            <div class="mt-6 hidden rounded-3xl border border-blue-100 bg-blue-50 p-5 shadow-lg" id="demoResult"></div>
        </section>

        <section class="mt-7 rounded-[2rem] border border-blue-100 bg-white p-5 shadow-2xl sm:p-8" id="about">
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">About the System</p>
            <h2 class="mt-3 text-2xl font-black text-brand-950 sm:text-3xl">Civil Service Exam Reviewer</h2>
            <p class="mt-3 text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">This online reviewer system is designed to help aspiring civil servants prepare for the Career Service Examination through randomized practice quizzes, timed exams, and performance tracking.</p>
            <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 p-4 sm:p-5">
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Programmed by</p>
                <p class="mt-1 text-lg font-black text-brand-950 sm:text-xl">ANTHONIE FENY V. CATALAN</p>
                <p class="text-sm font-semibold text-slate-500">IS Programmer</p>
            </div>
        </section>
    </main>
    <script>
    (function () {
        const submitButton = document.getElementById('submitDemoQuiz');
        const resetButton = document.getElementById('resetDemoQuiz');
        const resultArea = document.getElementById('demoResult');
        const questions = Array.from(document.querySelectorAll('.demo-question'));
        const correctClasses = ['border-emerald-300', 'bg-emerald-50', 'text-emerald-900'];
        const incorrectClasses = ['border-red-300', 'bg-red-50', 'text-red-900'];

        if (!submitButton || !resetButton || !resultArea || questions.length === 0) return;

        function submitDemoQuiz() {
            let score = 0;

            questions.forEach((question, index) => {
                const answer = Number(question.dataset.answer);
                const selected = question.querySelector(`input[name="demo${index + 1}"]:checked`);
                const selectedValue = selected ? Number(selected.value) : null;
                const isCorrect = selectedValue === answer;

                if (isCorrect) score += 1;

                question.querySelectorAll('.demo-choice').forEach(choice => {
                    const choiceValue = Number(choice.dataset.choice);
                    choice.classList.remove(...correctClasses, ...incorrectClasses);
                    if (choiceValue === answer) choice.classList.add(...correctClasses);
                    if (selectedValue === choiceValue && !isCorrect) choice.classList.add(...incorrectClasses);
                });

                question.querySelector('.demo-explanation')?.classList.remove('hidden');
                question.querySelectorAll('input').forEach(input => input.disabled = true);
            });

            const percent = Math.round((score / questions.length) * 100);
            resultArea.classList.remove('hidden');
            resultArea.innerHTML = `
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Sample Quiz Score</p>
                <h3 class="mt-2 text-3xl font-black text-brand-950">You scored ${score}/${questions.length}</h3>
                <div class="mt-3 text-5xl font-black text-brand-700">${percent}%</div>
                <p class="mt-3 font-semibold leading-7 text-slate-600">${percent >= 80 ? 'Nice work. You are ready to try the full reviewer.' : 'Good start. Keep practicing to improve your score.'}</p>
                <div class="mt-5 rounded-3xl border border-yellow-200 bg-yellow-50 p-5">
                    <h4 class="text-2xl font-black text-brand-950">Want more practice?</h4>
                    <p class="mt-2 font-semibold leading-7 text-slate-700">Register now for more questionnaire access, randomized exams, timer options, scoring, and explanations.</p>
                    <a class="mt-4 inline-flex rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5" href="index.php">Register Now</a>
                </div>
            `;
            submitButton.disabled = true;
            submitButton.classList.add('opacity-80', 'cursor-not-allowed');
            resetButton.classList.remove('hidden');
            resultArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function resetDemoQuiz() {
            questions.forEach(question => {
                question.querySelectorAll('.demo-choice').forEach(choice => {
                    choice.classList.remove(...correctClasses, ...incorrectClasses);
                });
                question.querySelector('.demo-explanation')?.classList.add('hidden');
                question.querySelectorAll('input').forEach(input => {
                    input.checked = false;
                    input.disabled = false;
                });
            });
            resultArea.classList.add('hidden');
            resultArea.innerHTML = '';
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-80', 'cursor-not-allowed');
            resetButton.classList.add('hidden');
        }

        submitButton.addEventListener('click', submitDemoQuiz);
        resetButton.addEventListener('click', resetDemoQuiz);
    })();
    </script>
    <script src="js/loader.js"></script>
    <script src="js/toast.js"></script>
</body>
</html>
