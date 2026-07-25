<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<main class="mx-auto grid min-h-[calc(100vh-74px)] w-full max-w-md place-items-center px-4 py-8">
    <section class="w-full rounded-[2rem] border border-blue-100 bg-white/95 p-7 shadow-2xl sm:p-9">
        <div class="mb-4 grid h-16 w-16 place-items-center rounded-full border-4 border-yellow-400 bg-gradient-to-br from-white to-blue-100 font-black text-brand-900 shadow-lg">CSC</div>
        <h1 class="text-4xl font-black text-brand-950">Login</h1>
        <div class="mt-6 grid grid-cols-3 gap-2 rounded-2xl bg-blue-50 p-2">
            <button class="tab rounded-xl py-3 font-black text-brand-900 transition hover:bg-white/60 <?= $defaultTab === 'user' ? 'bg-white shadow-md' : '' ?>" type="button" data-tab="user">User</button>
            <button class="tab rounded-xl py-3 font-black text-brand-900 transition hover:bg-white/60 <?= $defaultTab === 'guest' ? 'bg-white shadow-md' : '' ?>" type="button" data-tab="guest">Guest</button>
            <button class="tab rounded-xl py-3 font-black text-brand-900 transition hover:bg-white/60 <?= $defaultTab === 'admin' ? 'bg-white shadow-md' : '' ?>" type="button" data-tab="admin">Admin</button>
        </div>

        <form method="post" action="<?= site_url('login') ?>" class="tab-panel mt-6 grid <?= $defaultTab === 'user' ? '' : 'hidden' ?> gap-5" id="user-panel">
            <input type="hidden" name="mode" value="user">
            <label class="grid gap-2 font-extrabold text-brand-950">Email Address <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="email" name="email" required placeholder="Enter your registered email"></label>
            <button class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" type="submit">Login to Reviewer</button>
        </form>

        <form method="post" action="<?= site_url('login') ?>" class="tab-panel mt-6 grid <?= $defaultTab === 'guest' ? '' : 'hidden' ?> gap-5" id="guest-panel">
            <input type="hidden" name="mode" value="guest">
            <div class="rounded-2xl border <?= $guestMode ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-700' ?> p-4 text-sm font-bold leading-6"><?= $guestMode ? 'Guest access is open. Enter any nickname to use the reviewer.' : 'Guest access is currently disabled by the admin.' ?></div>
            <label class="grid gap-2 font-extrabold text-brand-950">Nickname <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="nickname" maxlength="60" required placeholder="Enter your nickname"></label>
            <button class="rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60" type="submit" <?= $guestMode ? '' : 'disabled' ?>>Enter as Guest</button>
        </form>

        <form method="post" action="<?= site_url('login') ?>" class="tab-panel mt-6 grid <?= $defaultTab === 'admin' ? '' : 'hidden' ?> gap-5" id="admin-panel">
            <input type="hidden" name="mode" value="admin">
            <label class="grid gap-2 font-extrabold text-brand-950">Admin Username <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="text" name="username" value="feny" required></label>
            <label class="grid gap-2 font-extrabold text-brand-950">Admin Password <input class="rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-blue-100" type="password" name="password" required></label>
            <button class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-600 px-5 py-4 font-black text-white shadow-xl transition hover:-translate-y-0.5" type="submit">Login as Admin</button>
        </form>
    </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.tab').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(item => item.classList.remove('bg-white', 'shadow-md'));
    document.querySelectorAll('.tab-panel').forEach(item => item.classList.add('hidden'));
    button.classList.add('bg-white', 'shadow-md');
    document.getElementById(button.dataset.tab + '-panel').classList.remove('hidden');
}));
</script>
<?= $this->endSection() ?>
