<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<main class="mx-auto w-full max-w-6xl px-4 py-8">
    <section class="mb-6 rounded-[2rem] border border-blue-100 bg-white p-6 shadow-2xl">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Guest Access Control</p><h1 class="mt-2 text-3xl font-black text-brand-950">Guest Mode</h1><p class="mt-2 font-semibold text-slate-600"><?= $guestMode ? 'Guest access is enabled. Visitors can enter with a nickname only.' : 'Guest access is disabled. Visitors must use an approved account.' ?></p></div>
            <form method="post" action="<?= site_url('admin/action') ?>" class="shrink-0">
                <input type="hidden" name="action" value="toggle_guest_mode">
                <button class="flex w-full items-center justify-between gap-4 rounded-2xl border <?= $guestMode ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?> px-5 py-4 font-black shadow-lg transition hover:-translate-y-0.5 sm:w-64" type="submit"><span><?= $guestMode ? 'Guest Mode ON' : 'Guest Mode OFF' ?></span><span class="relative inline-flex h-8 w-14 rounded-full <?= $guestMode ? 'bg-emerald-600' : 'bg-red-500' ?> transition"><span class="absolute top-1 h-6 w-6 rounded-full bg-white shadow transition <?= $guestMode ? 'left-7' : 'left-1' ?>"></span></span></button>
            </form>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5" id="statCards">
        <button class="stat-card text-left rounded-[2rem] border-2 border-brand-600 bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="all"><strong class="block text-4xl font-black text-brand-700"><?= count($users) ?></strong><span class="font-extrabold text-slate-600">Total Users</span></button>
        <button class="stat-card text-left rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="pending"><strong class="block text-4xl font-black text-amber-600"><?= $pending ?></strong><span class="font-extrabold text-slate-600">Pending</span></button>
        <button class="stat-card text-left rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="confirmed"><strong class="block text-4xl font-black text-emerald-600"><?= $confirmed ?></strong><span class="font-extrabold text-slate-600">Active</span></button>
        <button class="stat-card text-left rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl transition hover:-translate-y-0.5" type="button" data-filter="disabled"><strong class="block text-4xl font-black text-red-600"><?= $disabled ?></strong><span class="font-extrabold text-slate-600">Disabled</span></button>
        <div class="rounded-[2rem] border-2 border-transparent bg-white p-6 shadow-xl"><strong class="block text-4xl font-black text-violet-600"><?= count($guestLogs) ?></strong><span class="font-extrabold text-slate-600">Guest Access</span></div>
    </section>

    <section class="mt-6 rounded-[2rem] border border-blue-100 bg-white p-6 shadow-2xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><h1 class="text-3xl font-black text-brand-950">Registered Users</h1><div class="relative"><svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg><input class="w-full rounded-2xl border border-slate-300 bg-slate-50 py-3 pl-10 pr-4 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100 sm:w-72" type="text" id="userSearch" placeholder="Search..."></div></div>
        <div class="custom-scrollbar mt-5 overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead><tr class="bg-blue-50 text-brand-950"><th class="p-4">Name</th><th class="p-4">Email</th><th class="p-4">Age</th><th class="p-4">Status</th><th class="p-4">Created</th><th class="p-4">Actions</th></tr></thead>
                <tbody>
                <?php if (!$users): ?><tr><td class="border-b border-slate-100 p-4 text-slate-600" colspan="6">No registered users yet.</td></tr><?php endif; ?>
                <?php foreach ($users as $user): ?>
                    <?php
                    $userStatus = $statusOf($user);
                    $statusClasses = ['pending' => 'bg-amber-50 text-amber-700', 'confirmed' => 'bg-emerald-50 text-emerald-700', 'disabled' => 'bg-red-50 text-red-700'][$userStatus] ?? 'bg-slate-50 text-slate-700';
                    $statusLabel = ['pending' => 'Pending', 'confirmed' => 'Active', 'disabled' => 'Disabled'][$userStatus] ?? ucfirst($userStatus);
                    ?>
                    <tr class="hover:bg-slate-50" data-status="<?= esc($userStatus) ?>">
                        <td class="border-b border-slate-100 p-4 font-bold"><?= esc($user['name'] ?? '') ?></td><td class="border-b border-slate-100 p-4"><?= esc($user['email'] ?? 'No email') ?></td><td class="border-b border-slate-100 p-4"><?= esc((string)($user['age'] ?? '')) ?></td><td class="border-b border-slate-100 p-4"><span class="rounded-full px-3 py-1 text-xs font-black <?= $statusClasses ?>"><?= $statusLabel ?></span></td><td class="border-b border-slate-100 p-4"><?= esc(substr($user['created_at'] ?? '', 0, 10)) ?></td>
                        <td class="border-b border-slate-100 p-4"><div class="flex flex-wrap gap-2">
                            <?php if ($userStatus === 'pending'): ?><form method="post" action="<?= site_url('admin/action') ?>"><input type="hidden" name="action" value="confirm"><input type="hidden" name="user_id" value="<?= esc($user['id'] ?? '') ?>"><button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-emerald-700" type="submit">Confirm</button></form><?php endif; ?>
                            <?php if ($userStatus === 'confirmed'): ?><form method="post" action="<?= site_url('admin/action') ?>"><input type="hidden" name="action" value="disable"><input type="hidden" name="user_id" value="<?= esc($user['id'] ?? '') ?>"><button class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-amber-600" type="submit">Disable</button></form><?php endif; ?>
                            <?php if ($userStatus === 'disabled'): ?><form method="post" action="<?= site_url('admin/action') ?>"><input type="hidden" name="action" value="enable"><input type="hidden" name="user_id" value="<?= esc($user['id'] ?? '') ?>"><button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-emerald-700" type="submit">Enable</button></form><?php endif; ?>
                            <form method="post" action="<?= site_url('admin/action') ?>" onsubmit="return confirm('Delete this user?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?= esc($user['id'] ?? '') ?>"><button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-black text-white shadow transition hover:bg-red-700" type="submit">Delete</button></form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 rounded-[2rem] border border-blue-100 bg-white p-6 shadow-2xl">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Access Logs</p><h1 class="mt-2 text-3xl font-black text-brand-950">Guest Access Logs</h1></div><span class="rounded-2xl bg-violet-50 px-4 py-3 text-sm font-black text-violet-700"><?= count($guestLogs) ?> total entries</span></div>
        <div class="custom-scrollbar mt-5 overflow-x-auto"><table class="w-full border-collapse text-left text-sm"><thead><tr class="bg-blue-50 text-brand-950"><th class="p-4">Nickname</th><th class="p-4">Accessed At</th></tr></thead><tbody><?php if (!$guestLogs): ?><tr><td class="border-b border-slate-100 p-4 text-slate-600" colspan="2">No guest access logs yet.</td></tr><?php endif; ?><?php foreach ($guestLogs as $log): ?><tr class="hover:bg-slate-50"><td class="border-b border-slate-100 p-4 font-bold"><?= esc($log['nickname'] ?? '') ?></td><td class="border-b border-slate-100 p-4"><?= esc($log['accessed_at'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div>
    </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let activeFilter = 'all';
function applyFilters() {
    const query = document.getElementById('userSearch').value.toLowerCase();
    document.querySelectorAll('table tbody tr[data-status]').forEach(row => {
        const status = row.getAttribute('data-status') || '';
        const text = row.textContent.toLowerCase();
        row.style.display = ((activeFilter === 'all' || status === activeFilter) && (query === '' || text.includes(query))) ? '' : 'none';
    });
}
document.querySelectorAll('.stat-card').forEach(card => card.addEventListener('click', function () {
    activeFilter = this.getAttribute('data-filter');
    document.querySelectorAll('.stat-card').forEach(c => { c.classList.remove('border-brand-600'); c.classList.add('border-transparent'); });
    this.classList.remove('border-transparent'); this.classList.add('border-brand-600'); applyFilters();
}));
document.getElementById('userSearch')?.addEventListener('input', applyFilters);
</script>
<?= $this->endSection() ?>
