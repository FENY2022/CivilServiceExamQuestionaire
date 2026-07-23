(function () {
    const exam = window.EXAM_DATA;
    if (!exam) return;

    const quizArea = document.getElementById('quizArea');
    const categoryNav = document.getElementById('categoryNav');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const timer = document.getElementById('timer');
    const mobileTimer = document.getElementById('mobileTimerValue');
    const submitButton = document.getElementById('submitQuiz');
    const submitBadge = document.getElementById('submitBadge');
    const resultArea = document.getElementById('resultArea');
    const modalRoot = document.getElementById('modalRoot');
    const answers = new Map();
    const totalQuestions = exam.categories.reduce((sum, category) => sum + category.questions.length, 0);
    let remaining = exam.timeLimitMinutes * 60;
    let submitted = false;

    const navBase = 'w-full rounded-2xl px-3 py-3 text-left text-sm font-black text-slate-700 transition hover:bg-blue-50 hover:text-brand-700';
    const navActive = 'bg-blue-50 text-brand-700 ring-1 ring-blue-100';
    const correctClasses = ['border-emerald-300', 'bg-emerald-50', 'text-emerald-900'];
    const incorrectClasses = ['border-red-300', 'bg-red-50', 'text-red-900'];

    function renderNavigation() {
        categoryNav.innerHTML = exam.categories.map((category, index) =>
            `<button type="button" data-target="${category.key}" class="${navBase} ${index === 0 ? navActive : ''}">${index + 1}. ${escapeHtml(category.title)}</button>`
        ).join('');

        categoryNav.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', () => {
                setActiveNav(button);
                document.getElementById(button.dataset.target).scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function setActiveNav(activeButton) {
        categoryNav.querySelectorAll('button').forEach(button => {
            navActive.split(' ').forEach(className => button.classList.remove(className));
        });
        navActive.split(' ').forEach(className => activeButton.classList.add(className));
    }

    function renderQuiz() {
        let questionNumber = 1;
        quizArea.innerHTML = exam.categories.map(category => {
            const questions = category.questions.map(question => {
                const number = questionNumber++;
                const choices = question.choices.map((choice, index) => `
                    <label class="choice my-2 flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-question="${question.id}" data-choice="${index}">
                        <input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="${question.id}" value="${index}">
                        <span class="font-semibold leading-6">${String.fromCharCode(65 + index)}. ${escapeHtml(choice)}</span>
                    </label>
                `).join('');

                return `
                    <article class="question-card mb-4 rounded-3xl border border-blue-100 bg-white p-6 shadow-lg" data-question-id="${question.id}" data-answer="${question.answer}" data-number="${number}">
                        <h3 class="text-lg font-black leading-7 text-brand-950">${number}. ${escapeHtml(question.question)}</h3>
                        <div class="choices mt-4">${choices}</div>
                        <p class="explanation hidden mt-4 rounded-2xl bg-slate-50 p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> ${escapeHtml(question.explanation)}</p>
                    </article>
                `;
            }).join('');

            return `
                <section id="${category.key}" class="category-section scroll-mt-28">
                    <p class="mb-2 text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">${escapeHtml(category.group.replaceAll('_', ' '))}</p>
                    <h2 class="mb-4 text-2xl font-black text-brand-950">${escapeHtml(category.title)}</h2>
                    ${questions}
                </section>
            `;
        }).join('');

        quizArea.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', event => {
                answers.set(event.target.name, Number(event.target.value));
                updateProgress();
            });
        });
    }

    function updateProgress() {
        const answered = answers.size;
        const unanswered = totalQuestions - answered;
        progressText.textContent = `${answered} of ${totalQuestions} answered`;
        progressBar.style.width = `${(answered / totalQuestions) * 100}%`;
        if (submitBadge) submitBadge.textContent = `${unanswered} unanswered`;
    }

    function startTimer() {
        updateTimer();
        setInterval(() => {
            if (remaining <= 0 || submitted) return;
            remaining -= 1;
            updateTimer();
            if (remaining === 0) {
                if (window.showToast) showToast('Time is up. Your exam has been submitted automatically.', 'warning');
                submitQuiz();
            }
        }, 1000);
    }

    function updateTimer() {
        const hours = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;
        const formattedTime = [hours, minutes, seconds].map(value => String(value).padStart(2, '0')).join(':');
        timer.textContent = formattedTime;
        if (mobileTimer) mobileTimer.textContent = formattedTime;
    }

    function showSubmitModal() {
        if (submitted) return;
        const summary = buildSummary();
        const percent = Math.round((summary.answered / totalQuestions) * 1000) / 10;
        const rows = summary.categories.map(category => {
            const complete = category.answered === category.total;
            return `
                <tr class="border-b border-slate-100 last:border-0">
                    <td class="p-3 font-bold text-slate-800">${escapeHtml(category.title)}</td>
                    <td class="p-3 font-black text-brand-700">${category.answered}/${category.total}</td>
                    <td class="p-3"><span class="rounded-full px-3 py-1 text-xs font-black ${complete ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'}">${complete ? 'Complete' : `${category.total - category.answered} unanswered`}</span></td>
                </tr>
            `;
        }).join('');

        modalRoot.innerHTML = `
            <div class="fixed inset-0 z-[9998] grid place-items-center bg-slate-950/45 p-4 backdrop-blur-sm" id="submitModalBackdrop">
                <section class="modal-enter custom-scrollbar max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[2rem] bg-white p-6 shadow-2xl sm:p-8" role="dialog" aria-modal="true" aria-labelledby="submitModalTitle">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Exam Submission Summary</p>
                            <h2 class="mt-2 text-3xl font-black text-brand-950" id="submitModalTitle">Review Before Final Submission</h2>
                        </div>
                        <button type="button" class="rounded-2xl bg-slate-100 px-4 py-2 text-2xl font-black text-slate-600 transition hover:bg-slate-200" id="closeSubmitModal" aria-label="Close modal">&times;</button>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl bg-blue-50 p-5"><span class="text-sm font-extrabold text-slate-500">Answered</span><strong class="mt-1 block text-3xl font-black text-brand-700">${summary.answered}/${totalQuestions}</strong></div>
                        <div class="rounded-3xl bg-amber-50 p-5"><span class="text-sm font-extrabold text-slate-500">Unanswered</span><strong class="mt-1 block text-3xl font-black text-amber-700">${summary.unanswered}</strong></div>
                        <div class="rounded-3xl bg-emerald-50 p-5"><span class="text-sm font-extrabold text-slate-500">Completion</span><strong class="mt-1 block text-3xl font-black text-emerald-700">${percent}%</strong></div>
                    </div>

                    <div class="mt-5 h-4 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-gradient-to-r from-brand-700 to-yellow-400" style="width:${percent}%"></div></div>

                    ${summary.unanswered > 0 ? `<div class="mt-5 rounded-3xl border border-amber-200 bg-amber-50 p-4 font-bold leading-7 text-amber-900">${summary.unanswered} question${summary.unanswered === 1 ? '' : 's'} are unanswered. Unanswered items will count as incorrect if you submit now.</div>` : `<div class="mt-5 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 font-bold text-emerald-900">All questions have answers. You can submit when ready.</div>`}

                    <div class="custom-scrollbar mt-6 max-h-[360px] overflow-y-auto rounded-3xl border border-slate-200">
                        <table class="w-full border-collapse text-left text-sm">
                            <thead class="sticky top-0 bg-blue-50 text-brand-950"><tr><th class="p-3">Category</th><th class="p-3">Answered</th><th class="p-3">Status</th></tr></thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>

                    <div class="mt-7 flex flex-col justify-end gap-3 sm:flex-row">
                        <button type="button" class="rounded-2xl border border-blue-200 bg-white px-5 py-3 font-black text-brand-700 transition hover:bg-blue-50" id="reviewAnswers">Review My Answers</button>
                        <button type="button" class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5" id="confirmSubmit">Submit Final</button>
                    </div>
                </section>
            </div>
        `;

        document.getElementById('closeSubmitModal').addEventListener('click', closeSubmitModal);
        document.getElementById('reviewAnswers').addEventListener('click', reviewAnswers);
        document.getElementById('confirmSubmit').addEventListener('click', () => {
            closeSubmitModal();
            submitQuiz();
        });
        document.getElementById('submitModalBackdrop').addEventListener('click', event => {
            if (event.target.id === 'submitModalBackdrop') closeSubmitModal();
        });
    }

    function buildSummary() {
        const categories = exam.categories.map(category => {
            const answered = category.questions.filter(question => answers.has(question.id)).length;
            return { title: category.title, answered, total: category.questions.length };
        });
        const answered = answers.size;
        return { answered, unanswered: totalQuestions - answered, categories };
    }

    function closeSubmitModal() {
        modalRoot.innerHTML = '';
    }

    function reviewAnswers() {
        closeSubmitModal();
        const firstUnanswered = exam.categories.flatMap(category => category.questions).find(question => !answers.has(question.id));
        if (firstUnanswered) {
            const card = document.querySelector(`[data-question-id="${firstUnanswered.id}"]`);
            card?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card?.classList.add('ring-4', 'ring-amber-200');
            window.setTimeout(() => card?.classList.remove('ring-4', 'ring-amber-200'), 1800);
        } else if (window.showToast) {
            showToast('All questions have answers. You can submit when ready.', 'success');
        }
    }

    function submitQuiz() {
        if (submitted) return;
        submitted = true;
        let score = 0;
        const categoryScores = {};

        exam.categories.forEach(category => {
            categoryScores[category.title] = { score: 0, total: category.questions.length };
            category.questions.forEach(question => {
                const selected = answers.get(question.id);
                const isCorrect = selected === question.answer;
                if (isCorrect) {
                    score += 1;
                    categoryScores[category.title].score += 1;
                }

                const card = document.querySelector(`[data-question-id="${question.id}"]`);
                if (!card) return;
                card.querySelectorAll('.choice').forEach(choice => {
                    const choiceIndex = Number(choice.dataset.choice);
                    if (choiceIndex === question.answer) choice.classList.add(...correctClasses);
                    if (selected === choiceIndex && !isCorrect) choice.classList.add(...incorrectClasses);
                });
                card.querySelector('.explanation').classList.remove('hidden');
            });
        });

        const percent = Math.round((score / totalQuestions) * 10000) / 100;
        const passed = percent >= exam.passingPercent;
        const breakdown = Object.entries(categoryScores).map(([category, result]) => {
            const categoryPercent = Math.round((result.score / result.total) * 100);
            return `<tr class="border-b border-slate-100 last:border-0"><td class="p-3 font-bold">${escapeHtml(category)}</td><td class="p-3 font-black text-brand-700">${result.score}/${result.total}</td><td class="p-3">${categoryPercent}%</td></tr>`;
        }).join('');

        resultArea.classList.remove('hidden');
        resultArea.innerHTML = `
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Results</p>
            <h2 class="mt-2 text-3xl font-black text-brand-950">${passed ? 'Passed Practice Benchmark' : 'Needs More Review'}</h2>
            <div class="mt-4 text-6xl font-black text-brand-700">${percent}%</div>
            <p class="mt-3 text-slate-600">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong>. Passing benchmark: ${exam.passingPercent}%.</p>
            <div class="custom-scrollbar mt-6 overflow-x-auto rounded-3xl border border-slate-200"><table class="w-full border-collapse text-left text-sm"><thead class="bg-blue-50 text-brand-950"><tr><th class="p-3">Category</th><th class="p-3">Score</th><th class="p-3">Percent</th></tr></thead><tbody>${breakdown}</tbody></table></div>
            <p class="mt-4 text-sm font-semibold text-slate-500">Correct answers and explanations are now shown below each item.</p>
        `;
        resultArea.scrollIntoView({ behavior: 'smooth' });
        submitButton.disabled = true;
        submitButton.classList.add('opacity-80', 'cursor-not-allowed');
        submitButton.querySelector('span').textContent = 'Submitted';
        if (submitBadge) submitBadge.textContent = `${score}/${totalQuestions}`;
        if (window.showToast) showToast('Exam submitted successfully.', 'success');
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modalRoot.innerHTML) closeSubmitModal();
    });

    submitButton.addEventListener('click', showSubmitModal);

    renderNavigation();
    renderQuiz();
    updateProgress();
    startTimer();
})();
