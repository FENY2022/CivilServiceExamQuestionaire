<?php
use App\Models\SettingsModel;

$isAdmin = session()->has('admin');
$isUser = session()->has('user');
$isGuest = session()->has('guest');
$isLoggedIn = $isAdmin || $isUser || $isGuest;
$homeHref = $isLoggedIn ? site_url('dashboard') : site_url('/');
$links = [
    ['key' => 'home', 'label' => 'Home', 'href' => $homeHref],
    ['key' => 'about', 'label' => 'About', 'href' => site_url('about')],
];

if ($isAdmin) {
    $links[] = ['key' => 'admin', 'label' => 'Admin Panel', 'href' => site_url('admin')];
    $links[] = ['key' => 'logout', 'label' => 'Logout', 'href' => site_url('logout')];
} elseif ($isUser || $isGuest) {
    $links[] = ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => site_url('dashboard')];
    $links[] = ['key' => 'logout', 'label' => 'Logout', 'href' => site_url('logout')];
} elseif ((new SettingsModel())->isGuestMode()) {
    $links[] = ['key' => 'guest', 'label' => 'Guest Access', 'href' => site_url('login?guest=1')];
    $links[] = ['key' => 'login', 'label' => 'Admin Login', 'href' => site_url('login?admin=1')];
} else {
    $links[] = ['key' => 'login', 'label' => 'Login', 'href' => site_url('login')];
    $links[] = ['key' => 'register', 'label' => 'Register', 'href' => site_url('/')];
}
?>
<header class="sticky top-0 z-40 flex min-h-[74px] flex-col gap-3 border-b border-blue-100 bg-white/90 px-4 py-4 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:px-7">
    <a class="flex items-center gap-3 font-black text-brand-950" href="<?= esc($homeHref) ?>"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-700 text-white shadow-lg">CSC</span><span class="hidden sm:block"><?= esc($title ?? config('App')->appName) ?></span></a>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <?= $extraHtml ?? '' ?>
        <div class="relative shrink-0" id="textSizeWidget">
            <button type="button" id="textSizeToggle" class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl border border-blue-100 bg-white text-base font-black text-brand-700 shadow-sm transition hover:bg-blue-50" aria-label="Text size settings" aria-haspopup="true" aria-expanded="false" title="Adjust text size">A</button>
            <div id="textSizePanel" class="absolute right-0 z-50 mt-2 w-64 rounded-3xl border border-blue-100 bg-white p-5 shadow-2xl" role="dialog" aria-label="Text size settings" hidden>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm font-extrabold text-slate-500">Text Size</span>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-black text-brand-700" id="textSizeValue" aria-live="polite">100%</span>
                </div>
                <input type="range" id="textSizeRange" min="75" max="200" step="5" value="100" class="mt-4 w-full cursor-pointer" aria-label="Adjust text size">
                <div class="mt-2 flex justify-between text-xs font-black text-slate-400"><span>A</span><span aria-hidden="true">A</span></div>
                <button type="button" id="textSizeReset" class="mt-4 w-full rounded-2xl border border-blue-200 bg-white px-4 py-2 text-sm font-black text-brand-700 transition hover:bg-blue-50">Reset to 100%</button>
            </div>
        </div>
        <nav class="flex flex-wrap gap-4 text-sm font-black text-brand-700">
            <?php foreach ($links as $link): ?>
                <a class="<?= ($active ?? '') === $link['key'] ? 'text-brand-950 underline decoration-yellow-400 decoration-4 underline-offset-8' : 'hover:text-brand-950' ?>" href="<?= esc($link['href']) ?>"><?= esc($link['label']) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
