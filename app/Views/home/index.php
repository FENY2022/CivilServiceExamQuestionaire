<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
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

    <?php if ($guestMode): ?>
    <section class="w-full rounded-[2rem] border border-blue-100 bg-white/95 p-7 shadow-2xl sm:p-9">
        <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Guest Access</p>
        <h2 class="mt-3 text-3xl font-black text-brand-950">Enter as Guest</h2>
        <p class="mt-3 text-slate-600">Guest Mode is enabled. Registration is closed for now. Enter a nickname only to access the full reviewer.</p>
        <form method="post" action="<?= site_url('login') ?>" class="mt-7 grid gap-5">
            <input type="hidden" name="mode" value="guest">
            <label class="grid gap-2 font-extrabold text-brand-950">Nickname <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="nickname" maxlength="60" required placeholder="Enter your nickname"></label>
            <button type="submit" class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5 hover:shadow-blue-300/50">Enter Reviewer</button>
        </form>
        <div class="mt-6 text-sm font-black text-brand-700"><a class="hover:text-brand-950" href="<?= site_url('login?admin=1') ?>">Admin Login</a></div>
    </section>
    <?php else: ?>
    <section class="w-full rounded-[2rem] border border-blue-100 bg-white/95 p-7 shadow-2xl sm:p-9">
        <h2 class="text-3xl font-black text-brand-950">Create Reviewer Account</h2>
        <p class="mt-2 text-slate-600">Register your details first. Admin approval is required before you can access the reviewer.</p>
        <form method="post" action="<?= site_url('register') ?>" class="mt-7 grid gap-5" id="registerForm">
            <label class="grid gap-2 font-extrabold text-brand-950">Full Name <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="name" required placeholder="Anthonie Feny V. Catalan"></label>
            <label class="grid gap-2 font-extrabold text-brand-950">Email Address <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="email" name="email" required placeholder="you@example.com"></label>
            <label class="grid gap-2 font-extrabold text-brand-950">Age <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="number" name="age" min="1" required placeholder="Enter your age"></label>
            <label class="grid gap-2 font-extrabold text-brand-950">Math CAPTCHA <span class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-slate-700">What is <?= $captchaA ?> + <?= $captchaB ?>?</span><input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="number" name="captcha" required placeholder="Enter answer"></label>
            <button type="submit" class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5 hover:shadow-blue-300/50">Register</button>
        </form>
        <div class="mt-6 flex flex-col justify-between gap-3 text-sm font-black text-brand-700 sm:flex-row">
            <a class="hover:text-brand-950" href="<?= site_url('login') ?>">Already approved? Login</a>
            <a class="hover:text-brand-950" href="<?= site_url('login?admin=1') ?>">Admin Login</a>
        </div>
    </section>
    <?php endif; ?>

    <section class="w-full rounded-[2rem] border border-blue-100 bg-white/95 p-7 shadow-2xl sm:p-9 lg:col-span-2" id="sampleQuiz">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Free Demo</p>
                <h2 class="mt-3 text-3xl font-black text-brand-950">Try a Free Sample Questionnaire</h2>
                <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Answer 5 sample questions and see your score right away before registering.</p>
            </div>
            <div class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-black text-brand-700">5 sample questions</div>
        </div>

        <div class="mt-6 grid gap-4" id="publicDemoQuiz">
            <article class="public-demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="1">
                <h3 class="text-lg font-black leading-7 text-brand-950">1. Choose the correct sentence.</h3>
                <div class="mt-4 grid gap-2">
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo1" value="0">A. She do her work carefully.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo1" value="1">B. She does her work carefully.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo1" value="2">C. She doing her work carefully.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo1" value="3">D. She done her work carefully.</label>
                </div>
                <p class="public-demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> Singular subject "she" uses "does".</p>
            </article>
            <article class="public-demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="0">
                <h3 class="text-lg font-black leading-7 text-brand-950">2. PANACEA most nearly means:</h3>
                <div class="mt-4 grid gap-2">
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo2" value="0">A. cure-all</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo2" value="1">B. conflict</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo2" value="2">C. standard</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo2" value="3">D. warning</label>
                </div>
                <p class="public-demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> A panacea is a remedy for all problems.</p>
            </article>
            <article class="public-demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="1">
                <h3 class="text-lg font-black leading-7 text-brand-950">3. What is 4 x 8 + 12 / 4 - 8 / 2?</h3>
                <div class="mt-4 grid gap-2">
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo3" value="0">A. 30</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo3" value="1">B. 31</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo3" value="2">C. 35</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo3" value="3">D. 39</label>
                </div>
                <p class="public-demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> Apply order of operations: 32 + 3 - 4 = 31.</p>
            </article>
            <article class="public-demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="0">
                <h3 class="text-lg font-black leading-7 text-brand-950">4. All luxuries are needless expenditures. Cable TV is a luxury. Therefore:</h3>
                <div class="mt-4 grid gap-2">
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo4" value="0">A. Cable TV is needless expenditure.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo4" value="1">B. All expenditures are luxuries.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo4" value="2">C. Cable TV is free.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo4" value="3">D. No conclusion.</label>
                </div>
                <p class="public-demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> This follows directly by syllogism.</p>
            </article>
            <article class="public-demo-question rounded-3xl border border-blue-100 bg-blue-50/40 p-5" data-answer="0">
                <h3 class="text-lg font-black leading-7 text-brand-950">5. A passage says pollutants cross national borders through wind. What is the best conclusion?</h3>
                <div class="mt-4 grid gap-2">
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="0"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo5" value="0">A. Pollution can affect distant places.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="1"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo5" value="1">B. Pollution stays in one country.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="2"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo5" value="2">C. Wind prevents pollution.</label>
                    <label class="public-demo-choice flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-choice="3"><input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="publicDemo5" value="3">D. Only cities are polluted.</label>
                </div>
                <p class="public-demo-explanation mt-4 hidden rounded-2xl bg-white p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> The main idea is that pollutants can travel far.</p>
            </article>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <button class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" id="submitPublicDemoQuiz" type="button">Submit Sample Quiz</button>
            <button class="hidden rounded-2xl border border-blue-200 bg-white px-5 py-4 font-black text-brand-700 transition hover:bg-blue-50" id="resetPublicDemoQuiz" type="button">Try Again</button>
        </div>
        <div class="mt-6 hidden rounded-3xl border border-blue-100 bg-blue-50 p-5 shadow-lg" id="publicDemoResult"></div>
    </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const submitButton = document.getElementById('submitPublicDemoQuiz');
    const resetButton = document.getElementById('resetPublicDemoQuiz');
    const resultArea = document.getElementById('publicDemoResult');
    const registerForm = document.getElementById('registerForm');
    const questions = Array.from(document.querySelectorAll('.public-demo-question'));
    const correctClasses = ['border-emerald-300', 'bg-emerald-50', 'text-emerald-900'];
    const incorrectClasses = ['border-red-300', 'bg-red-50', 'text-red-900'];

    if (!submitButton || !resetButton || !resultArea || questions.length === 0) return;

    function submitDemoQuiz() {
        let score = 0;
        questions.forEach((question, index) => {
            const answer = Number(question.dataset.answer);
            const selected = question.querySelector(`input[name="publicDemo${index + 1}"]:checked`);
            const selectedValue = selected ? Number(selected.value) : null;
            const isCorrect = selectedValue === answer;
            if (isCorrect) score += 1;
            question.querySelectorAll('.public-demo-choice').forEach(choice => {
                const choiceValue = Number(choice.dataset.choice);
                choice.classList.remove(...correctClasses, ...incorrectClasses);
                if (choiceValue === answer) choice.classList.add(...correctClasses);
                if (selectedValue === choiceValue && !isCorrect) choice.classList.add(...incorrectClasses);
            });
            question.querySelector('.public-demo-explanation')?.classList.remove('hidden');
            question.querySelectorAll('input').forEach(input => input.disabled = true);
        });
        const percent = Math.round((score / questions.length) * 100);
        resultArea.classList.remove('hidden');
        resultArea.innerHTML = '<p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Your Sample Score</p><h3 class="mt-2 text-3xl font-black text-brand-950">You scored ' + score + '/' + questions.length + '</h3><div class="mt-3 text-5xl font-black text-brand-700">' + percent + '%</div><p class="mt-3 font-semibold leading-7 text-slate-600">' + (percent >= 80 ? 'Nice score. You can practice more with the full reviewer.' : 'Good start. Register now and improve with more practice questions.') + '</p><div class="mt-5 rounded-3xl border border-yellow-200 bg-yellow-50 p-5"><h4 class="text-2xl font-black text-brand-950">Want more questions?</h4><p class="mt-2 font-semibold leading-7 text-slate-700">Register now to access the full reviewer with more questionnaires, timer options, scoring, and explanations.</p><button class="mt-4 rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5" id="demoRegisterNow" type="button">Register Now</button></div>';
        document.getElementById('demoRegisterNow')?.addEventListener('click', () => {
            registerForm?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            registerForm?.querySelector('input[name="name"]')?.focus({ preventScroll: true });
        });
        submitButton.disabled = true;
        submitButton.classList.add('opacity-80', 'cursor-not-allowed');
        resetButton.classList.remove('hidden');
        resultArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetDemoQuiz() {
        questions.forEach(question => {
            question.querySelectorAll('.public-demo-choice').forEach(choice => choice.classList.remove(...correctClasses, ...incorrectClasses));
            question.querySelector('.public-demo-explanation')?.classList.add('hidden');
            question.querySelectorAll('input').forEach(input => { input.checked = false; input.disabled = false; });
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
<?= $this->endSection() ?>
