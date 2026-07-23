(function () {
    const exam = window.EXAM_DATA;
    if (!exam) return;

    const quizArea = document.getElementById('quizArea');
    const categoryNav = document.getElementById('categoryNav');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const timer = document.getElementById('timer');
    const submitButton = document.getElementById('submitQuiz');
    const resultArea = document.getElementById('resultArea');
    const answers = new Map();
    const totalQuestions = exam.categories.reduce((sum, category) => sum + category.questions.length, 0);
    let remaining = exam.timeLimitMinutes * 60;

    function renderNavigation() {
        categoryNav.innerHTML = exam.categories.map((category, index) =>
            `<button type="button" data-target="${category.key}" class="${index === 0 ? 'active' : ''}">${index + 1}. ${category.title}</button>`
        ).join('');

        categoryNav.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.category-nav button').forEach(item => item.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(button.dataset.target).scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function renderQuiz() {
        let questionNumber = 1;
        quizArea.innerHTML = exam.categories.map(category => {
            const questions = category.questions.map(question => {
                const number = questionNumber++;
                const choices = question.choices.map((choice, index) => `
                    <label class="choice" data-question="${question.id}" data-choice="${index}">
                        <input type="radio" name="${question.id}" value="${index}">
                        <span>${String.fromCharCode(65 + index)}. ${escapeHtml(choice)}</span>
                    </label>
                `).join('');

                return `
                    <article class="question-card" data-question-id="${question.id}" data-answer="${question.answer}">
                        <h3>${number}. ${escapeHtml(question.question)}</h3>
                        <div class="choices">${choices}</div>
                        <p class="explanation hidden"><strong>Explanation:</strong> ${escapeHtml(question.explanation)}</p>
                    </article>
                `;
            }).join('');

            return `<section id="${category.key}" class="category-section"><p class="eyebrow">${category.group.replaceAll('_', ' ')}</p><h2>${escapeHtml(category.title)}</h2>${questions}</section>`;
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
        progressText.textContent = `${answered} of ${totalQuestions} answered`;
        progressBar.style.width = `${(answered / totalQuestions) * 100}%`;
    }

    function startTimer() {
        updateTimer();
        setInterval(() => {
            if (remaining <= 0) return;
            remaining -= 1;
            updateTimer();
            if (remaining === 0) submitQuiz();
        }, 1000);
    }

    function updateTimer() {
        const hours = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;
        timer.textContent = [hours, minutes, seconds].map(value => String(value).padStart(2, '0')).join(':');
    }

    function submitQuiz() {
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
                    if (choiceIndex === question.answer) choice.classList.add('correct');
                    if (selected === choiceIndex && !isCorrect) choice.classList.add('incorrect');
                });
                card.querySelector('.explanation').classList.remove('hidden');
            });
        });

        const percent = Math.round((score / totalQuestions) * 10000) / 100;
        const passed = percent >= exam.passingPercent;
        const breakdown = Object.entries(categoryScores).map(([category, result]) => {
            const categoryPercent = Math.round((result.score / result.total) * 100);
            return `<tr><td>${escapeHtml(category)}</td><td>${result.score}/${result.total}</td><td>${categoryPercent}%</td></tr>`;
        }).join('');

        resultArea.classList.remove('hidden');
        resultArea.innerHTML = `
            <p class="eyebrow">Results</p>
            <h2>${passed ? 'Passed Practice Benchmark' : 'Needs More Review'}</h2>
            <div class="result-score">${percent}%</div>
            <p>You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong>. Passing benchmark: ${exam.passingPercent}%.</p>
            <div class="table-wrap"><table><thead><tr><th>Category</th><th>Score</th><th>Percent</th></tr></thead><tbody>${breakdown}</tbody></table></div>
            <p class="muted">Correct answers and explanations are now shown below each item.</p>
        `;
        resultArea.scrollIntoView({ behavior: 'smooth' });
        submitButton.disabled = true;
        submitButton.textContent = 'Submitted';
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    submitButton.addEventListener('click', () => {
        if (answers.size < totalQuestions && !confirm(`You answered ${answers.size} of ${totalQuestions}. Submit anyway?`)) return;
        submitQuiz();
    });

    renderNavigation();
    renderQuiz();
    updateProgress();
    startTimer();
})();
