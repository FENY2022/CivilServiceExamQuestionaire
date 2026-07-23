(function () {
    const styles = {
        success: {
            wrap: 'border-emerald-200 bg-emerald-50 text-emerald-900',
            icon: 'bg-emerald-600 text-white',
            symbol: '✓',
            timeout: 4000,
        },
        error: {
            wrap: 'border-red-200 bg-red-50 text-red-900',
            icon: 'bg-red-600 text-white',
            symbol: '!',
            timeout: 6500,
        },
        warning: {
            wrap: 'border-amber-200 bg-amber-50 text-amber-900',
            icon: 'bg-amber-500 text-white',
            symbol: '!',
            timeout: 6000,
        },
        info: {
            wrap: 'border-blue-200 bg-blue-50 text-blue-900',
            icon: 'bg-blue-600 text-white',
            symbol: 'i',
            timeout: 4500,
        },
    };

    function getContainer() {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'fixed right-4 top-4 z-[9999] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3 sm:right-6 sm:top-6';
            document.body.appendChild(container);
        }
        return container;
    }

    function dismiss(toast) {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-leave');
        window.setTimeout(() => toast.remove(), 180);
    }

    window.showToast = function showToast(message, type = 'info') {
        const config = styles[type] || styles.info;
        const toast = document.createElement('div');
        toast.className = `toast-enter flex items-start gap-3 rounded-2xl border px-4 py-3 shadow-2xl backdrop-blur ${config.wrap}`;
        toast.innerHTML = `
            <div class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-full text-sm font-black ${config.icon}">${config.symbol}</div>
            <div class="min-w-0 flex-1 text-sm font-semibold leading-6">${escapeHtml(message)}</div>
            <button type="button" class="rounded-lg px-2 py-1 text-lg font-black leading-none opacity-70 transition hover:bg-black/5 hover:opacity-100" aria-label="Dismiss notification">&times;</button>
        `;
        toast.querySelector('button').addEventListener('click', () => dismiss(toast));
        getContainer().appendChild(toast);
        window.setTimeout(() => dismiss(toast), config.timeout);
    };

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
})();
