<?php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? '';
    $users = get_users();

    if ($action === 'delete') {
        $users = array_filter($users, fn($user) => ($user['id'] ?? '') !== $userId);
    } else {
        foreach ($users as &$user) {
            if (($user['id'] ?? '') !== $userId) {
                continue;
            }

            if ($action === 'confirm') {
                $user['status'] = 'confirmed';
                $user['confirmed_at'] = date('c');
                $user['disabled_at'] = null;
            } elseif ($action === 'disable') {
                $user['status'] = 'disabled';
                $user['disabled_at'] = date('c');
            } elseif ($action === 'enable') {
                $user['status'] = 'confirmed';
                $user['confirmed_at'] = $user['confirmed_at'] ?? date('c');
                $user['disabled_at'] = null;
            }
        }
        unset($user);
    }

    save_users($users);
    header('Location: admin.php');
    exit;
}

$users = get_users();
$statusOf = function (array $user): string {
    return $user['status'] ?? (!empty($user['confirmed']) ? 'confirmed' : 'pending');
};
$pending = count(array_filter($users, fn($user) => $statusOf($user) === 'pending'));
$confirmed = count(array_filter($users, fn($user) => $statusOf($user) === 'confirmed'));
$disabled = count(array_filter($users, fn($user) => $statusOf($user) === 'disabled'));
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
    <?php render_top_nav('Admin Panel', 'admin'); ?>
    <main class="mx-auto w-full max-w-6xl px-4 py-8">
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" id="statCards">
            <button class="stat-card text-left rounded-[2rem] border-2 border-brand-600 bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="all"><strong class="block text-4xl font-black text-brand-700"><?= count($users) ?></strong><span class="font-extrabold text-slate-600">Total Users</span></button>
            <button class="stat-card text-left rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="pending"><strong class="block text-4xl font-black text-amber-600"><?= $pending ?></strong><span class="font-extrabold text-slate-600">Pending</span></button>
            <button class="stat-card text-left rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="confirmed"><strong class="block text-4xl font-black text-emerald-600"><?= $confirmed ?></strong><span class="font-extrabold text-slate-600">Active</span></button>
            <button class="stat-card text-left rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="disabled"><strong class="block text-4xl font-black text-red-600"><?= $disabled ?></strong><span class="font-extrabold text-slate-600">Disabled</span></button>
        </section>
        <section class="mt-6 rounded-[2rem] border border-blue-100 bg-white p-6 shadow-2xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-3xl font-black text-brand-950">Registered Users</h1>
                <div class="relative">
                    <input class="w-full rounded-2xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100 sm:w-72" type="text" id="userSearch" placeholder="Search by name, email, or status...">
                    <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                </div>
            </div>
            <div class="custom-scrollbar mt-5 overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead><tr class="bg-blue-50 text-brand-950"><th class="p-4">Name</th><th class="p-4">Email</th><th class="p-4">Age</th><th class="p-4">Status</th><th class="p-4">Created</th><th class="p-4">Actions</th></tr></thead>
                    <tbody>
                    <?php if (!$users): ?>
                        <tr><td class="border-b border-slate-100 p-4 text-slate-600" colspan="6">No registered users yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                            $userStatus = $statusOf($user);
                            $statusClasses = [
                                'pending' => 'bg-amber-50 text-amber-700',
                                'confirmed' => 'bg-emerald-50 text-emerald-700',
                                'disabled' => 'bg-red-50 text-red-700',
                            ][$userStatus] ?? 'bg-slate-50 text-slate-700';
                            $statusLabel = ['pending' => 'Pending', 'confirmed' => 'Active', 'disabled' => 'Disabled'][$userStatus] ?? ucfirst($userStatus);
                        ?>
                        <tr class="hover:bg-slate-50" data-status="<?= $userStatus ?>">
                            <td class="border-b border-slate-100 p-4 font-bold"><?= htmlspecialchars($user['name'] ?? '') ?></td>
                            <td class="border-b border-slate-100 p-4"><?= htmlspecialchars($user['email'] ?? 'No email') ?></td>
                            <td class="border-b border-slate-100 p-4"><?= htmlspecialchars((string)($user['age'] ?? '')) ?></td>
                            <td class="border-b border-slate-100 p-4"><span class="rounded-full px-3 py-1 text-xs font-black <?= $statusClasses ?>"><?= $statusLabel ?></span></td>
                            <td class="border-b border-slate-100 p-4"><?= htmlspecialchars(substr($user['created_at'] ?? '', 0, 10)) ?></td>
                            <td class="border-b border-slate-100 p-4">
                                <div class="flex flex-wrap gap-2">
                                    <?php if ($userStatus === 'pending'): ?>
                                        <form method="post"><input type="hidden" name="action" value="confirm"><input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>"><button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-emerald-700" type="submit">Confirm</button></form>
                                    <?php elseif ($userStatus === 'confirmed'): ?>
                                        <form method="post"><input type="hidden" name="action" value="disable"><input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>"><button class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-amber-600" type="submit">Disable</button></form>
                                    <?php elseif ($userStatus === 'disabled'): ?>
                                        <form method="post"><input type="hidden" name="action" value="enable"><input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>"><button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-emerald-700" type="submit">Enable</button></form>
                                    <?php endif; ?>
                                    <form method="post" onsubmit="return confirm('Delete this user?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>"><button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-red-700" type="submit">Delete</button></form>
                                </div>
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
    <script>
    let activeFilter = 'all';

    function applyFilters() {
        const query = document.getElementById('userSearch').value.toLowerCase();
        document.querySelectorAll('table tbody tr').forEach(function(row) {
            const status = row.getAttribute('data-status') || '';
            const text = row.textContent.toLowerCase();
            const matchFilter = activeFilter === 'all' || status === activeFilter;
            const matchSearch = query === '' || text.includes(query);
            row.style.display = (matchFilter && matchSearch) ? '' : 'none';
        });
    }

    document.querySelectorAll('.stat-card').forEach(function(card) {
        card.addEventListener('click', function() {
            activeFilter = this.getAttribute('data-filter');
            document.querySelectorAll('.stat-card').forEach(function(c) {
                c.classList.remove('border-brand-600');
                c.classList.add('border-transparent');
            });
            this.classList.remove('border-transparent');
            this.classList.add('border-brand-600');
            applyFilters();
        });
    });

    document.getElementById('userSearch')?.addEventListener('input', applyFilters);
    </script>
</body>
</html>
