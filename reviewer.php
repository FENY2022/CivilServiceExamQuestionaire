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
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="reviewer-page">
    <header class="topbar sticky">
        <a class="brand" href="dashboard.php"><span>CSC</span><?= ucfirst($type) ?> Reviewer</a>
        <nav><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></nav>
    </header>
    <main class="review-layout">
        <aside class="review-sidebar">
            <div class="timer-card">
                <span>Time Remaining</span>
                <strong id="timer">--:--:--</strong>
            </div>
            <div class="progress-card">
                <span id="progressText">0 answered</span>
                <div class="progress"><div id="progressBar"></div></div>
            </div>
            <nav id="categoryNav" class="category-nav"></nav>
        </aside>
        <section class="review-main">
            <div class="quiz-heading">
                <div>
                    <p class="eyebrow">Civil Service Exam Practice</p>
                    <h1><?= htmlspecialchars($exam['title']) ?></h1>
                </div>
                <button class="btn secondary" id="submitQuiz" type="button">Submit Quiz</button>
            </div>
            <div id="quizArea"></div>
            <div id="resultArea" class="result-area hidden"></div>
        </section>
    </main>
    <script>window.EXAM_DATA = <?= json_encode($exam, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
    <script src="js/app.js"></script>
</body>
</html>
