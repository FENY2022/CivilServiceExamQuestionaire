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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="dashboard.php"><span>CSC</span><?= APP_NAME ?></a>
        <nav>
            <?php if (!empty($_SESSION['admin'])): ?><a href="admin.php">Admin Panel</a><?php endif; ?>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main class="container">
        <section class="welcome-card">
            <p class="eyebrow">Online Review Center</p>
            <h1>Welcome, <?= htmlspecialchars(current_user_name()) ?></h1>
            <p>Select an exam type. Each reviewer includes 20 sample questions per topic, a countdown timer, scoring, and the 80% passing benchmark.</p>
        </section>

        <section class="exam-grid">
            <article class="exam-card professional">
                <div class="card-icon">P</div>
                <h2>Professional Level</h2>
                <p>For second-level government positions. Includes general ability, general information, and advanced critical reasoning.</p>
                <ul class="plain-list">
                    <li>23 categories</li>
                    <li>460 sample questions</li>
                    <li>Time limit: 3 hours 10 minutes</li>
                </ul>
                <a class="btn primary" href="reviewer.php?type=professional">Start Professional Reviewer</a>
            </article>
            <article class="exam-card subprofessional">
                <div class="card-icon">S</div>
                <h2>Subprofessional Level</h2>
                <p>For first-level government positions. Includes general ability, general information, and clerical operations.</p>
                <ul class="plain-list">
                    <li>24 categories</li>
                    <li>480 sample questions</li>
                    <li>Time limit: 2 hours 40 minutes</li>
                </ul>
                <a class="btn secondary" href="reviewer.php?type=subprofessional">Start Subprofessional Reviewer</a>
            </article>
        </section>
    </main>
</body>
</html>
