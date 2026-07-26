(function () {
    const exam = window.EXAM_DATA;
    if (!exam) return;

    const quizArea = document.getElementById('quizArea');
    const categoryNav = document.getElementById('categoryNav');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const timer = document.getElementById('timer');
    const mobileTimer = document.getElementById('mobileTimer');
    const submitButton = document.getElementById('submitQuiz');
    const resultArea = document.getElementById('resultArea');
    const modalRoot = document.getElementById('modalRoot');
    const answers = new Map();
    const sectionResults = new Map();
    const descriptiveCategory = buildDescriptiveCategory();
    const categories = descriptiveCategory ? [descriptiveCategory, ...exam.categories] : exam.categories;
    const totalQuestions = exam.categories.reduce((sum, category) => sum + category.questions.length, 0);
    const timerEnabled = exam.timerEnabled !== false;
    const storageKey = `civilServiceReviewer:${exam.type}:progress:v2`;
    let activeCategoryKey = categories[0]?.key || '';
    let remaining = exam.timeLimitMinutes * 60;
    let submitted = false;

    const navBase = 'w-full rounded-2xl px-3 py-3 text-left text-sm font-black text-slate-700 transition hover:bg-blue-50 hover:text-brand-700';
    const navActive = 'bg-blue-50 text-brand-700 ring-1 ring-blue-100';
    const correctClasses = ['border-emerald-300', 'bg-emerald-50', 'text-emerald-900'];
    const incorrectClasses = ['border-red-300', 'bg-red-50', 'text-red-900'];

    function renderNavigation() {
        categoryNav.innerHTML = categories.map((category, index) => {
            const result = sectionResults.get(category.key);
            const answered = category.questions.filter(question => answers.has(question.id)).length;
            const status = result ? `${result.score}/${result.total}` : `${answered}/${category.questions.length}`;
            const statusLabel = result ? 'Scored' : 'Answered';
            return `<button type="button" data-target="${category.key}" class="${navBase} ${category.key === activeCategoryKey ? navActive : ''}">
                <span class="block">${index + 1}. ${escapeHtml(category.title)}</span>
                <span class="mt-1 block text-xs font-extrabold ${result ? 'text-emerald-700' : 'text-slate-400'}">${statusLabel}: ${status}</span>
            </button>`;
        }).join('');

        categoryNav.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', () => activateCategory(button.dataset.target));
        });
    }

    function activateCategory(categoryKey) {
        activeCategoryKey = categoryKey;
        document.querySelectorAll('.category-section').forEach(section => {
            section.classList.toggle('hidden', section.id !== categoryKey);
        });
        categoryNav.querySelectorAll('button').forEach(button => {
            navActive.split(' ').forEach(className => button.classList.toggle(className, button.dataset.target === categoryKey));
        });
        saveProgress();
        updateProgress();
        document.getElementById(categoryKey)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function renderQuiz() {
        quizArea.innerHTML = categories.map(category => {
            const scored = isScoredCategory(category);
            const questions = category.questions.map((question, index) => {
                const choices = question.choices.map((choice, choiceIndex) => `
                    <label class="choice my-2 flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-3 text-slate-700 transition hover:border-blue-200 hover:bg-blue-50" data-question="${question.id}" data-choice="${choiceIndex}">
                        <input class="mt-1 h-4 w-4 shrink-0 accent-brand-700" type="radio" name="${question.id}" value="${choiceIndex}" data-category="${category.key}">
                        <span class="font-semibold leading-6">${String.fromCharCode(65 + choiceIndex)}. ${escapeHtml(choice)}</span>
                    </label>
                `).join('');

                return `
                    <article class="question-card mb-4 rounded-3xl border border-blue-100 bg-white p-6 shadow-lg" data-question-id="${question.id}" data-answer="${question.answer}" data-number="${index + 1}">
                        <h3 class="text-lg font-black leading-7 text-brand-950">${index + 1}. ${escapeHtml(question.question)}</h3>
                        ${question.note ? `<p class="mt-2 text-sm font-bold text-slate-500">${escapeHtml(question.note)}</p>` : ''}
                        <div class="choices mt-4">${choices}</div>
                        ${scored ? `<p class="explanation hidden mt-4 rounded-2xl bg-slate-50 p-4 text-sm font-semibold leading-6 text-slate-600"><strong class="text-brand-950">Explanation:</strong> ${escapeHtml(question.explanation)}</p>` : ''}
                    </article>
                `;
            }).join('');

            return `
                <section id="${category.key}" class="category-section scroll-mt-28 ${category.key === activeCategoryKey ? '' : 'hidden'}" data-category="${category.key}">
                    <div class="mb-5 rounded-3xl border border-blue-100 bg-white p-6 shadow-xl">
                        <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">${escapeHtml(category.group.replaceAll('_', ' '))}</p>
                        <h2 class="mt-2 text-3xl font-black text-brand-950">${escapeHtml(category.title)}</h2>
                        <p class="mt-2 font-semibold text-slate-600">${scored ? 'Answer all questions in this section, then submit this section to get your score immediately.' : escapeHtml(exam.descriptiveInstructions || 'Supply the information as honestly and accurately as possible. This section is not scored.')}</p>
                    </div>
                    ${questions}
                    ${scored ? `<div class="section-result mb-5 hidden rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-lg" data-result="${category.key}"></div>
                    <button type="button" class="section-submit mb-8 rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-6 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" data-category="${category.key}">Submit ${escapeHtml(category.title)}</button>` : ''}
                </section>
            `;
        }).join('');

        quizArea.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', event => {
                if (sectionResults.has(event.target.dataset.category)) return;
                answers.set(event.target.name, Number(event.target.value));
                saveProgress();
                updateProgress();
                renderNavigation();
            });
        });

        quizArea.querySelectorAll('.section-submit').forEach(button => {
            button.addEventListener('click', () => submitSection(button.dataset.category));
        });
    }

    function updateProgress() {
        const activeCategory = getCategory(activeCategoryKey);
        if (!activeCategory) return;
        const answered = activeCategory.questions.filter(question => answers.has(question.id)).length;
        const total = activeCategory.questions.length;
        const overallAnswered = exam.categories.reduce(
            (sum, category) => sum + category.questions.filter(question => answers.has(question.id)).length,
            0
        );
        const overallUnanswered = totalQuestions - overallAnswered;
        progressText.textContent = `${answered} of ${total} answered in ${activeCategory.title}`;
        progressBar.style.width = `${total === 0 ? 0 : (answered / total) * 100}%`;
        const currentSubmitBadge = document.getElementById('submitBadge');
        if (currentSubmitBadge) currentSubmitBadge.textContent = `${sectionResults.size}/${exam.categories.length} sections scored`;
        if (!submitted && overallUnanswered === 0 && sectionResults.size === exam.categories.length && window.showToast) {
            showToast('All sections have been scored. You can view the overall summary.', 'success');
        }
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
        const timeText = [hours, minutes, seconds].map(value => String(value).padStart(2, '0')).join(':');
        timer.textContent = timeText;
        if (mobileTimer) mobileTimer.textContent = timeText;
    }

    function showNoTimerLimit() {
        if (timer) timer.textContent = 'No limit';
        if (mobileTimer) mobileTimer.textContent = 'No limit';
    }

    function showSubmitModal() {
        if (submitted) return;
        const summary = buildSummary();
        const percent = Math.round((summary.score / totalQuestions) * 10000) / 100;
        const rows = summary.categories.map(category => `
            <tr class="border-b border-slate-100 last:border-0">
                <td class="p-3 font-bold text-slate-800">${escapeHtml(category.title)}</td>
                <td class="p-3 font-black text-brand-700">${category.result ? `${category.result.score}/${category.result.total}` : 'Not scored'}</td>
                <td class="p-3"><span class="rounded-full px-3 py-1 text-xs font-black ${category.result ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'}">${category.result ? `${category.result.percent}%` : `${category.answered}/${category.total} answered`}</span></td>
            </tr>
        `).join('');

        modalRoot.innerHTML = `
            <div class="fixed inset-0 z-[9998] grid place-items-center bg-slate-950/45 p-4 backdrop-blur-sm" id="submitModalBackdrop">
                <section class="modal-enter custom-scrollbar max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[2rem] bg-white p-6 shadow-2xl sm:p-8" role="dialog" aria-modal="true" aria-labelledby="submitModalTitle">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Overall Summary</p>
                            <h2 class="mt-2 text-3xl font-black text-brand-950" id="submitModalTitle">Section Scores</h2>
                        </div>
                        <button type="button" class="rounded-2xl bg-slate-100 px-4 py-2 text-2xl font-black text-slate-600 transition hover:bg-slate-200" id="closeSubmitModal" aria-label="Close modal">&times;</button>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl bg-blue-50 p-5"><span class="text-sm font-extrabold text-slate-500">Scored Sections</span><strong class="mt-1 block text-3xl font-black text-brand-700">${sectionResults.size}/${exam.categories.length}</strong></div>
                        <div class="rounded-3xl bg-emerald-50 p-5"><span class="text-sm font-extrabold text-slate-500">Current Score</span><strong class="mt-1 block text-3xl font-black text-emerald-700">${summary.score}/${totalQuestions}</strong></div>
                        <div class="rounded-3xl bg-amber-50 p-5"><span class="text-sm font-extrabold text-slate-500">Overall Percent</span><strong class="mt-1 block text-3xl font-black text-amber-700">${percent}%</strong></div>
                    </div>

                    ${summary.unscored > 0 ? `<div class="mt-5 rounded-3xl border border-amber-200 bg-amber-50 p-4 font-bold leading-7 text-amber-900">${summary.unscored} section${summary.unscored === 1 ? '' : 's'} not scored yet. If you finalize now, unsubmitted sections will be scored as they are.</div>` : `<div class="mt-5 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 font-bold text-emerald-900">All sections have been scored.</div>`}

                    <div class="custom-scrollbar mt-6 max-h-[360px] overflow-y-auto rounded-3xl border border-slate-200">
                        <table class="w-full border-collapse text-left text-sm">
                            <thead class="sticky top-0 bg-blue-50 text-brand-950"><tr><th class="p-3">Section</th><th class="p-3">Score</th><th class="p-3">Status</th></tr></thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>

                    <div class="mt-7 flex flex-col justify-end gap-3 sm:flex-row">
                        <button type="button" class="rounded-2xl border border-blue-200 bg-white px-5 py-3 font-black text-brand-700 transition hover:bg-blue-50" id="reviewAnswers">Review Active Section</button>
                        <button type="button" class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5" id="confirmSubmit">Finalize Overall Result</button>
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
            return { title: category.title, key: category.key, answered, total: category.questions.length, result: sectionResults.get(category.key) };
        });
        const score = categories.reduce((sum, category) => sum + (category.result?.score || 0), 0);
        const unscored = categories.filter(category => !category.result).length;
        return { score, unscored, categories };
    }

    function closeSubmitModal() {
        modalRoot.innerHTML = '';
    }

    function reviewAnswers() {
        closeSubmitModal();
        const activeCategory = getCategory(activeCategoryKey);
        const firstUnanswered = activeCategory?.questions.find(question => !answers.has(question.id));
        if (firstUnanswered) {
            const card = document.querySelector(`[data-question-id="${firstUnanswered.id}"]`);
            card?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card?.classList.add('ring-4', 'ring-amber-200');
            window.setTimeout(() => card?.classList.remove('ring-4', 'ring-amber-200'), 1800);
        } else if (window.showToast) {
            showToast('The active section has answers for all questions.', 'success');
        }
    }

    function submitSection(categoryKey) {
        if (sectionResults.has(categoryKey)) return;
        const category = getCategory(categoryKey);
        if (!category) return;
        const unanswered = category.questions.filter(question => !answers.has(question.id)).length;
        if (unanswered > 0) {
            showConfirmModal({
                eyebrow: 'Section Confirmation',
                title: `Submit ${category.title}?`,
                message: `${unanswered} question${unanswered === 1 ? '' : 's'} unanswered in ${category.title}. Submit this section now?`,
                confirmLabel: 'Submit Section',
                cancelLabel: 'Cancel',
                onConfirm: () => finalizeSection(category),
            });
            return;
        }
        finalizeSection(category);
    }

    function finalizeSection(category) {
        if (sectionResults.has(category.key)) return;
        const result = scoreCategory(category);
        sectionResults.set(category.key, result);
        showSectionResult(category, result);
        lockSection(category.key);
        saveProgress();
        updateProgress();
        renderNavigation();
        if (window.showToast) showToast(`${category.title} scored: ${result.score}/${result.total} (${result.percent}%).`, 'success');
    }

    function showConfirmModal({ eyebrow, title, message, confirmLabel, cancelLabel, onConfirm }) {
        modalRoot.innerHTML = `
            <div class="fixed inset-0 z-[9998] grid place-items-center bg-slate-950/45 p-4 backdrop-blur-sm" id="confirmModalBackdrop">
                <section class="modal-enter w-full max-w-xl rounded-[2rem] bg-white p-6 shadow-2xl sm:p-8" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-amber-600">${escapeHtml(eyebrow)}</p>
                            <h2 class="mt-2 text-3xl font-black text-brand-950" id="confirmModalTitle">${escapeHtml(title)}</h2>
                        </div>
                        <button type="button" class="rounded-2xl bg-slate-100 px-4 py-2 text-2xl font-black text-slate-600 transition hover:bg-slate-200" id="closeConfirmModal" aria-label="Close modal">&times;</button>
                    </div>

                    <div class="mt-6 rounded-3xl border border-amber-200 bg-amber-50 p-5 font-bold leading-7 text-amber-900">${escapeHtml(message)}</div>

                    <div class="mt-7 flex flex-col justify-end gap-3 sm:flex-row">
                        <button type="button" class="rounded-2xl border border-blue-200 bg-white px-5 py-3 font-black text-brand-700 transition hover:bg-blue-50" id="cancelConfirmModal">${escapeHtml(cancelLabel)}</button>
                        <button type="button" class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5" id="acceptConfirmModal">${escapeHtml(confirmLabel)}</button>
                    </div>
                </section>
            </div>
        `;

        const close = () => closeSubmitModal();
        document.getElementById('closeConfirmModal').addEventListener('click', close);
        document.getElementById('cancelConfirmModal').addEventListener('click', close);
        document.getElementById('acceptConfirmModal').addEventListener('click', () => {
            closeSubmitModal();
            onConfirm();
        });
        document.getElementById('confirmModalBackdrop').addEventListener('click', event => {
            if (event.target.id === 'confirmModalBackdrop') close();
        });
    }

    function submitQuiz() {
        if (submitted) return;
        submitted = true;
        if (window.showButtonLoading) showButtonLoading(submitButton, 'Finalizing...');
        if (window.showPageLoader) showPageLoader('Finalizing overall result, please wait...');

        exam.categories.forEach(category => {
            if (!sectionResults.has(category.key)) {
                const result = scoreCategory(category);
                sectionResults.set(category.key, result);
                showSectionResult(category, result);
                lockSection(category.key);
            }
        });

        const score = Array.from(sectionResults.values()).reduce((sum, result) => sum + result.score, 0);
        const percent = Math.round((score / totalQuestions) * 10000) / 100;
        const passed = percent >= exam.passingPercent;
        const breakdown = exam.categories.map(category => {
            const result = sectionResults.get(category.key);
            return `<tr class="border-b border-slate-100 last:border-0"><td class="p-3 font-bold">${escapeHtml(category.title)}</td><td class="p-3 font-black text-brand-700">${result.score}/${result.total}</td><td class="p-3">${result.percent}%</td></tr>`;
        }).join('');

        resultArea.classList.remove('hidden');
        resultArea.innerHTML = `
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Overall Results</p>
            <h2 class="mt-2 text-3xl font-black text-brand-950">${passed ? 'Passed Practice Benchmark' : 'Needs More Review'}</h2>
            <div class="mt-4 text-6xl font-black text-brand-700">${percent}%</div>
            <p class="mt-3 text-slate-600">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong>. Passing benchmark: ${exam.passingPercent}%.</p>
            <div class="custom-scrollbar mt-6 overflow-x-auto rounded-3xl border border-slate-200"><table class="w-full border-collapse text-left text-sm"><thead class="bg-blue-50 text-brand-950"><tr><th class="p-3">Section</th><th class="p-3">Score</th><th class="p-3">Percent</th></tr></thead><tbody>${breakdown}</tbody></table></div>
            <p class="mt-4 text-sm font-semibold text-slate-500">Correct answers and explanations are shown in each section. Submitted sections are locked.</p>
        `;
        resultArea.scrollIntoView({ behavior: 'smooth' });
        submitButton.disabled = true;
        submitButton.classList.add('opacity-80', 'cursor-not-allowed');
        submitButton.innerHTML = `<span>Finalized</span><span class="ml-2 rounded-full bg-white/20 px-2 py-1 text-xs">${score}/${totalQuestions}</span>`;
        saveProgress();
        updateProgress();
        renderNavigation();
        if (window.hidePageLoader) window.setTimeout(hidePageLoader, 250);
        if (window.showToast) showToast('Overall result finalized.', 'success');
    }

    function scoreCategory(category) {
        let score = 0;
        category.questions.forEach(question => {
            const selected = answers.get(question.id);
            const isCorrect = selected === question.answer;
            if (isCorrect) score += 1;

            const card = document.querySelector(`[data-question-id="${question.id}"]`);
            if (!card) return;
            card.querySelectorAll('.choice').forEach(choice => {
                const choiceIndex = Number(choice.dataset.choice);
                if (choiceIndex === question.answer) choice.classList.add(...correctClasses);
                if (selected === choiceIndex && !isCorrect) choice.classList.add(...incorrectClasses);
            });
            card.querySelector('.explanation').classList.remove('hidden');
        });
        const total = category.questions.length;
        const percent = Math.round((score / total) * 10000) / 100;
        return { score, total, percent };
    }

    function showSectionResult(category, result, scrollToResult = true) {
        const resultBox = document.querySelector(`[data-result="${category.key}"]`);
        if (!resultBox) return;
        const passed = result.percent >= exam.passingPercent;
        const nextCategory = getNextCategory(category.key);
        resultBox.classList.remove('hidden');
        resultBox.innerHTML = `
            <p class="text-xs font-extrabold uppercase tracking-[.18em] ${passed ? 'text-emerald-700' : 'text-amber-700'}">Section Score</p>
            <h3 class="mt-2 text-3xl font-black text-brand-950">${escapeHtml(category.title)}: ${result.score}/${result.total}</h3>
            <p class="mt-2 text-5xl font-black ${passed ? 'text-emerald-700' : 'text-amber-700'}">${result.percent}%</p>
            <p class="mt-3 font-semibold text-slate-600">${passed ? 'Good section performance.' : 'Review this section again.'} Answers are now locked for this section.</p>
            ${nextCategory ? `<button type="button" class="next-section mt-5 rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5" data-next-category="${nextCategory.key}">Next: ${escapeHtml(nextCategory.title)}</button>` : `<p class="mt-5 rounded-2xl bg-blue-50 p-4 font-bold text-brand-700">This is the last section. You can open the overall summary when ready.</p>`}
        `;
        resultBox.querySelector('.next-section')?.addEventListener('click', event => {
            activateCategory(event.currentTarget.dataset.nextCategory);
        });
        if (scrollToResult) resultBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function lockSection(categoryKey) {
        const section = document.getElementById(categoryKey);
        section?.querySelectorAll('input[type="radio"]').forEach(input => {
            input.disabled = true;
        });
        const button = section?.querySelector('.section-submit');
        if (button) {
            button.disabled = true;
            button.classList.add('opacity-80', 'cursor-not-allowed');
            button.textContent = 'Section Submitted';
        }
    }

    function getCategory(categoryKey) {
        return categories.find(category => category.key === categoryKey);
    }

    function getNextCategory(categoryKey) {
        const index = categories.findIndex(category => category.key === categoryKey);
        return index >= 0 ? categories[index + 1] : null;
    }

    function isScoredCategory(category) {
        return category.scored !== false;
    }

    function buildDescriptiveCategory() {
        if (!Array.isArray(exam.descriptiveQuestionnaire) || exam.descriptiveQuestionnaire.length === 0) return null;
        return {
            key: 'descriptive_questionnaire',
            title: 'Descriptive Questionnaire',
            group: 'examinee_profile',
            scored: false,
            questions: exam.descriptiveQuestionnaire,
        };
    }

    function saveProgress() {
        try {
            localStorage.setItem(storageKey, JSON.stringify({
                activeCategoryKey,
                submitted,
                answers: Object.fromEntries(answers),
                sectionResults: Object.fromEntries(sectionResults),
            }));
        } catch (error) {
            if (window.showToast) showToast('Progress could not be saved in this browser.', 'warning');
        }
    }

    function restoreProgress() {
        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
            if (!saved || typeof saved !== 'object') return;

            Object.entries(saved.answers || {}).forEach(([questionId, answer]) => {
                answers.set(questionId, Number(answer));
            });
            Object.entries(saved.sectionResults || {}).forEach(([categoryKey, result]) => {
                sectionResults.set(categoryKey, {
                    score: Number(result.score || 0),
                    total: Number(result.total || 0),
                    percent: Number(result.percent || 0),
                });
            });
            if (getCategory(saved.activeCategoryKey)) activeCategoryKey = saved.activeCategoryKey;
            submitted = Boolean(saved.submitted);
        } catch (error) {
            localStorage.removeItem(storageKey);
        }
    }

    function applyRestoredProgress() {
        answers.forEach((answer, questionId) => {
            const input = document.querySelector(`input[name="${questionId}"][value="${answer}"]`);
            if (input) input.checked = true;
        });

        exam.categories.forEach(category => {
            const result = sectionResults.get(category.key);
            if (!result) return;
            scoreCategory(category);
            showSectionResult(category, result, false);
            lockSection(category.key);
        });

        if (submitted) {
            renderFinalizedResult();
        }
    }

    function clearProgressAndRetake() {
        localStorage.removeItem(storageKey);
        window.location.reload();
    }

    function setupActionBar() {
        const actionBar = document.createElement('div');
        actionBar.id = 'quizActionBar';
        actionBar.className = 'fixed inset-x-3 bottom-3 z-50 flex flex-row-reverse items-center justify-end gap-2 transition duration-200 sm:inset-x-auto sm:bottom-4 sm:right-7 sm:gap-3';
        document.body.appendChild(actionBar);

        submitButton.className = 'block rounded-full bg-gradient-to-r from-brand-950 to-brand-700 px-4 py-3 text-sm font-black text-white shadow-2xl shadow-blue-900/25 transition hover:-translate-y-1 hover:shadow-blue-700/40 sm:px-6 sm:py-4 sm:text-base';
        actionBar.appendChild(submitButton);
    }

    function updateActionBarVisibility() {
        const actionBar = document.getElementById('quizActionBar');
        if (!actionBar) return;
        const isMobile = window.matchMedia('(max-width: 639px)').matches;
        const distanceFromBottom = document.documentElement.scrollHeight - (window.scrollY + window.innerHeight);
        const shouldShow = !isMobile || distanceFromBottom <= 80;

        actionBar.classList.toggle('opacity-0', !shouldShow);
        actionBar.classList.toggle('translate-y-6', !shouldShow);
        actionBar.classList.toggle('pointer-events-none', !shouldShow);
    }

    function renderRetakeButton() {
        const actionBar = document.getElementById('quizActionBar');
        const retakeButton = document.createElement('button');
        retakeButton.id = 'retakeExam';
        retakeButton.type = 'button';
        retakeButton.className = 'rounded-full border border-blue-200 bg-white px-4 py-3 text-sm font-black text-brand-700 shadow-2xl shadow-blue-900/10 transition hover:-translate-y-1 hover:bg-blue-50 sm:px-6 sm:py-4 sm:text-base';
        retakeButton.textContent = 'Retake Exam';
        retakeButton.addEventListener('click', () => {
            showConfirmModal({
                eyebrow: 'Retake Confirmation',
                title: 'Retake Exam?',
                message: 'This will clear your saved answers and section scores for this exam. Start again?',
                confirmLabel: 'Retake Exam',
                cancelLabel: 'Cancel',
                onConfirm: clearProgressAndRetake,
            });
        });
        actionBar.appendChild(retakeButton);
    }

    function renderFinalizedResult() {
        const score = Array.from(sectionResults.values()).reduce((sum, result) => sum + result.score, 0);
        const percent = Math.round((score / totalQuestions) * 10000) / 100;
        const passed = percent >= exam.passingPercent;
        const breakdown = exam.categories.map(category => {
            const result = sectionResults.get(category.key) || scoreCategory(category);
            return `<tr class="border-b border-slate-100 last:border-0"><td class="p-3 font-bold">${escapeHtml(category.title)}</td><td class="p-3 font-black text-brand-700">${result.score}/${result.total}</td><td class="p-3">${result.percent}%</td></tr>`;
        }).join('');

        resultArea.classList.remove('hidden');
        resultArea.innerHTML = `
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Overall Results</p>
            <h2 class="mt-2 text-3xl font-black text-brand-950">${passed ? 'Passed Practice Benchmark' : 'Needs More Review'}</h2>
            <div class="mt-4 text-6xl font-black text-brand-700">${percent}%</div>
            <p class="mt-3 text-slate-600">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong>. Passing benchmark: ${exam.passingPercent}%.</p>
            <div class="custom-scrollbar mt-6 overflow-x-auto rounded-3xl border border-slate-200"><table class="w-full border-collapse text-left text-sm"><thead class="bg-blue-50 text-brand-950"><tr><th class="p-3">Section</th><th class="p-3">Score</th><th class="p-3">Percent</th></tr></thead><tbody>${breakdown}</tbody></table></div>
            <p class="mt-4 text-sm font-semibold text-slate-500">Correct answers and explanations are shown in each section. Submitted sections are locked.</p>
        `;
        submitButton.disabled = true;
        submitButton.classList.add('opacity-80', 'cursor-not-allowed');
        submitButton.innerHTML = `<span>Finalized</span><span class="ml-2 rounded-full bg-white/20 px-2 py-1 text-xs">${score}/${totalQuestions}</span>`;
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
    window.addEventListener('scroll', updateActionBarVisibility, { passive: true });
    window.addEventListener('resize', updateActionBarVisibility);

    setupActionBar();
    submitButton.innerHTML = '<span>Overall Summary</span><span class="ml-1 rounded-full bg-white/20 px-2 py-0.5 text-[11px] sm:ml-2 sm:py-1 sm:text-xs" id="submitBadge">0 sections scored</span>';
    submitButton.addEventListener('click', showSubmitModal);

    restoreProgress();
    renderQuiz();
    applyRestoredProgress();
    renderNavigation();
    renderRetakeButton();
    updateProgress();
    updateActionBarVisibility();
    if (timerEnabled) {
        startTimer();
    } else {
        showNoTimerLimit();
    }
})();
