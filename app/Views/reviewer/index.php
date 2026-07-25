<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<main class="mx-auto grid w-full max-w-[1440px] gap-5 px-4 pb-40 pt-5 lg:grid-cols-[320px_1fr]">
    <aside class="grid gap-4 self-start lg:sticky lg:top-24">
        <div class="hidden rounded-3xl border border-blue-100 bg-white p-5 shadow-xl lg:block"><span class="text-sm font-extrabold text-slate-500">Time Remaining</span><strong class="mt-1 block text-3xl font-black text-brand-700" id="timer">--:--:--</strong></div>
        <div class="rounded-3xl border border-blue-100 bg-white p-5 shadow-xl"><span class="text-sm font-extrabold text-slate-500" id="progressText">0 answered</span><div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-200"><div class="h-full w-0 rounded-full bg-gradient-to-r from-brand-700 to-yellow-400 transition-all duration-300" id="progressBar"></div></div></div>
        <nav id="categoryNav" class="custom-scrollbar max-h-[58vh] overflow-y-auto rounded-3xl border border-blue-100 bg-white p-3 shadow-xl"></nav>
    </aside>
    <section class="grid min-w-0 gap-5">
        <div class="rounded-3xl border border-blue-100 bg-white p-6 shadow-xl"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-brand-600">Civil Service Exam Practice</p><h1 class="mt-2 text-4xl font-black text-brand-950"><?= esc($exam['title']) ?></h1></div>
        <div id="quizArea"></div>
        <div id="resultArea" class="hidden rounded-3xl border border-blue-100 bg-white p-7 shadow-xl"></div>
    </section>
</main>
<button class="hidden" id="submitQuiz" type="button"><span>Submit Exam</span><span class="ml-2 rounded-full bg-white/20 px-2 py-1 text-xs" id="submitBadge">0 unanswered</span></button>
<div id="modalRoot"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>window.EXAM_DATA = <?= json_encode($exam, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="<?= base_url('js/app.js') ?>?v=<?= @filemtime(ROOTPATH . 'public/js/app.js') ?: time() ?>"></script>
<?= $this->endSection() ?>
