(function () {
    let loader;

    function getLoader() {
        if (loader) return loader;

        loader = document.createElement('div');
        loader.className = 'page-loader is-visible';
        loader.setAttribute('role', 'status');
        loader.setAttribute('aria-live', 'polite');
        loader.innerHTML = `
            <div class="page-loader__card">
                <span class="page-loader__spinner" aria-hidden="true"></span>
                <span class="page-loader__text">Loading, please wait...</span>
            </div>
        `;
        document.body.appendChild(loader);
        return loader;
    }

    function showPageLoader(text) {
        const element = getLoader();
        const label = element.querySelector('.page-loader__text');
        if (label && text) label.textContent = text;
        element.classList.add('is-visible');
    }

    function hidePageLoader() {
        if (!loader) return;
        loader.classList.remove('is-visible');
    }

    function showButtonLoading(button, text) {
        if (!button || button.classList.contains('is-button-loading')) return;
        button.dataset.originalHtml = button.innerHTML;
        button.classList.add('is-button-loading');
        button.setAttribute('aria-busy', 'true');
        if ('disabled' in button) button.disabled = true;
        button.innerHTML = `<span class="button-loader__spinner" aria-hidden="true"></span>${text || 'Loading...'}`;
    }

    function hideButtonLoading(button) {
        if (!button || !button.dataset.originalHtml) return;
        button.innerHTML = button.dataset.originalHtml;
        delete button.dataset.originalHtml;
        button.classList.remove('is-button-loading');
        button.removeAttribute('aria-busy');
        if ('disabled' in button) button.disabled = false;
    }

    function shouldLoadLink(link) {
        if (!link || link.target || link.hasAttribute('download')) return false;
        const href = link.getAttribute('href') || '';
        return href !== '' && href !== '#' && !href.startsWith('javascript:') && !href.startsWith('mailto:') && !href.startsWith('tel:');
    }

    function attachHandlers() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', event => {
                window.setTimeout(() => {
                    if (event.defaultPrevented) return;
                    const button = form.querySelector('button[type="submit"], input[type="submit"]');
                    showButtonLoading(button, button?.dataset.loadingText || 'Processing...');
                    showPageLoader('Processing, please wait...');
                }, 0);
            });
        });

        document.addEventListener('click', event => {
            const link = event.target.closest('a[href]');
            if (shouldLoadLink(link)) {
                showButtonLoading(link, link.dataset.loadingText || 'Loading...');
                showPageLoader('Loading, please wait...');
                return;
            }

            const button = event.target.closest('button[onclick]');
            if (button && (button.getAttribute('onclick') || '').includes('location')) {
                showButtonLoading(button, button.dataset.loadingText || 'Loading...');
                showPageLoader('Loading, please wait...');
            }
        });
    }

    window.showPageLoader = showPageLoader;
    window.hidePageLoader = hidePageLoader;
    window.showButtonLoading = showButtonLoading;
    window.hideButtonLoading = hideButtonLoading;

    document.addEventListener('DOMContentLoaded', () => {
        getLoader();
        attachHandlers();
    });

    window.addEventListener('load', () => {
        window.setTimeout(hidePageLoader, 180);
    });

    window.addEventListener('pageshow', event => {
        if (event.persisted) hidePageLoader();
    });
})();
