<?php
require_once __DIR__ . '/config.php';

$token = trim($_GET['token'] ?? '');
$message = 'Invalid confirmation link.';
$status = 'error';

if ($token !== '') {
    $tokens = read_json_file(TOKENS_FILE);
    if (isset($tokens[$token])) {
        $entry = $tokens[$token];
        if (strtotime($entry['expires_at'] ?? '') < time()) {
            $message = 'This confirmation link has expired. Please register again.';
        } else {
            $users = get_users();
            foreach ($users as &$user) {
                if (strtolower($user['email'] ?? '') === strtolower($entry['email'] ?? '')) {
                    $user['confirmed'] = true;
                    $user['confirmed_at'] = date('c');
                    $_SESSION['user'] = $user;
                    $message = 'Your account is confirmed. You can now access the reviewer.';
                    $status = 'success';
                    break;
                }
            }
            unset($user);
            save_users($users);
            unset($tokens[$token]);
            write_json_file(TOKENS_FILE, $tokens);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="grid min-h-screen place-items-center bg-gradient-to-br from-blue-50 via-white to-sky-100 p-4 font-sans text-slate-900">
    <main class="w-full max-w-lg rounded-[2rem] border border-blue-100 bg-white/95 p-8 text-center shadow-2xl sm:p-10">
        <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full border-4 border-yellow-400 bg-gradient-to-br from-white to-blue-100 font-black text-brand-900 shadow-lg">CSC</div>
        <h1 class="text-3xl font-black text-brand-950">Registration Confirmation</h1>
        <p class="mx-auto mt-3 max-w-sm text-slate-600">Your registration request has been processed. Check the notification for the result.</p>
        <a class="mt-7 inline-flex rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-6 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" href="<?= $status === 'success' ? 'dashboard.php' : 'index.php' ?>"><?= $status === 'success' ? 'Go to Dashboard' : 'Back to Registration' ?></a>
    </main>
    <script src="js/toast.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($message) ?>, <?= json_encode($status) ?>));</script>
</body>
</html>
