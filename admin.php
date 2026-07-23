<?php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_email'])) {
    $email = strtolower(trim($_POST['delete_email']));
    $users = array_filter(get_users(), fn($user) => strtolower($user['email'] ?? '') !== $email);
    save_users($users);
    header('Location: admin.php');
    exit;
}

$users = get_users();
$confirmed = count(array_filter($users, fn($user) => !empty($user['confirmed'])));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef7ff', 600: '#1479c9', 700: '#0f5ea8', 900: '#123c69', 950: '#102a43' } } } } };
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/loader.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-blue-50 font-sans text-slate-900">
    <header class="sticky top-0 z-40 flex min-h-[74px] flex-col gap-3 border-b border-blue-100 bg-white/90 px-4 py-4 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:px-7">
        <a class="flex items-center gap-3 font-black text-brand-950" href="dashboard.php"><span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-700 text-white shadow-lg">CSC</span>Admin Panel</a>
        <nav class="flex gap-4 text-sm font-black text-brand-700"><a class="hover:text-brand-950" href="dashboard.php">Dashboard</a><a class="hover:text-brand-950" href="logout.php">Logout</a></nav>
    </header>
    <main class="mx-auto w-full max-w-6xl px-4 py-8">
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[2rem] border border-blue-100 bg-white p-6 shadow-xl"><strong class="block text-4xl font-black text-brand-700"><?= count($users) ?></strong><span class="font-extrabold text-slate-600">Total Users</span></div>
            <div class="rounded-[2rem] border border-blue-100 bg-white p-6 shadow-xl"><strong class="block text-4xl font-black text-emerald-600"><?= $confirmed ?></strong><span class="font-extrabold text-slate-600">Confirmed</span></div>
            <div class="rounded-[2rem] border border-blue-100 bg-white p-6 shadow-xl"><strong class="block text-4xl font-black text-amber-600"><?= count($users) - $confirmed ?></strong><span class="font-extrabold text-slate-600">Pending</span></div>
        </section>
        <section class="mt-6 rounded-[2rem] border border-blue-100 bg-white p-6 shadow-2xl">
            <h1 class="text-3xl font-black text-brand-950">Registered Users</h1>
            <div class="custom-scrollbar mt-5 overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead><tr class="bg-blue-50 text-brand-950"><th class="p-4">Name</th><th class="p-4">Age</th><th class="p-4">Email</th><th class="p-4">Status</th><th class="p-4">Created</th><th class="p-4">Action</th></tr></thead>
                    <tbody>
                    <?php if (!$users): ?>
                        <tr><td class="border-b border-slate-100 p-4 text-slate-600" colspan="6">No registered users yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="border-b border-slate-100 p-4 font-bold"><?= htmlspecialchars($user['name'] ?? '') ?></td>
                            <td class="border-b border-slate-100 p-4"><?= htmlspecialchars((string)($user['age'] ?? '')) ?></td>
                            <td class="border-b border-slate-100 p-4"><?= htmlspecialchars($user['email'] ?? '') ?></td>
                            <td class="border-b border-slate-100 p-4"><span class="rounded-full px-3 py-1 text-xs font-black <?= !empty($user['confirmed']) ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?>"><?= !empty($user['confirmed']) ? 'Confirmed' : 'Pending' ?></span></td>
                            <td class="border-b border-slate-100 p-4"><?= htmlspecialchars(substr($user['created_at'] ?? '', 0, 10)) ?></td>
                            <td class="border-b border-slate-100 p-4">
                                <form method="post" onsubmit="return confirm('Delete this user?')">
                                    <input type="hidden" name="delete_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                    <button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-red-700" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="js/loader.js"></script>
    <script src="js/toast.js"></script>
</body>
</html>
