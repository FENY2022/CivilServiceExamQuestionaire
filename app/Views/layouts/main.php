<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? config('App')->appName) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/loader.css') ?>">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50 font-sans text-slate-900">
    <?= view('layouts/partials/nav', ['active' => $active ?? '', 'title' => $navTitle ?? config('App')->appName, 'extraHtml' => $extraHtml ?? '']) ?>
    <?= $this->renderSection('content') ?>
    <script src="<?= base_url('js/loader.js') ?>"></script>
    <script src="<?= base_url('js/toast.js') ?>"></script>
    <?php if (!empty($message)): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($message) ?>, <?= json_encode($status ?? 'info') ?>));</script>
    <?php endif; ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
