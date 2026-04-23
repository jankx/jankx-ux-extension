/**
 * jux-preview entry point
 * Runs inside the preview iframe — listens for postMessage from the builder.
 */
(function () {
    'use strict';

    function applyRendered(items: Array<{ id: string; html: string; tag: string }>) {
        const container = document.getElementById('jux-builder-content');

        if (!container) {
            // Full-page preview — nothing to patch inline
            return;
        }

        // Clear existing content to avoid duplication and ensure correct order
        container.innerHTML = '';

        // Append all rendered elements in order
        items.forEach((item) => {
            container.insertAdjacentHTML('beforeend', item.html);
        });
    }

    function highlightElement(id: string) {
        document.querySelectorAll('.jux-element-highlight').forEach((el) =>
            el.classList.remove('jux-element-highlight')
        );
        const el = document.querySelector(`[data-jux-id="${id}"]`);
        if (el) el.classList.add('jux-element-highlight');
    }

    // Tell parent the iframe is ready
    window.addEventListener('load', () => {
        window.parent.postMessage({ action: 'jux-ready' }, '*');
    });

    // Listen for messages from the builder
    window.addEventListener('message', (e: MessageEvent) => {
        if (!e.data?.action) return;

        switch (e.data.action as string) {
            case 'jux-rendered':
                applyRendered(e.data.items ?? []);
                break;
            case 'jux-select':
                highlightElement(e.data.id ?? '');
                break;
        }
    });

    // Send click events on JUX elements back to builder
    document.addEventListener('click', (e: MouseEvent) => {
        const target = (e.target as Element).closest('[data-jux-id]');
        if (target) {
            e.preventDefault();
            e.stopPropagation();
            const id = target.getAttribute('data-jux-id') ?? '';
            window.parent.postMessage({ action: 'jux-element-click', id }, '*');
        }
    });
})();
