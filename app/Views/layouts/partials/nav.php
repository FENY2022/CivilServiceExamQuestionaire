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
        <nav class="flex flex-wrap gap-4 text-sm font-black text-brand-700">
            <?php foreach ($links as $link): ?>
                <a class="<?= ($active ?? '') === $link['key'] ? 'text-brand-950 underline decoration-yellow-400 decoration-4 underline-offset-8' : 'hover:text-brand-950' ?>" href="<?= esc($link['href']) ?>"><?= esc($link['label']) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
