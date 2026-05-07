@php
    $flashToasts = collect([
        session()->has('status') && filled(session('status')) ? ['variant' => 'success', 'text' => (string) session('status')] : null,
        session()->has('error') && filled(session('error')) ? ['variant' => 'danger', 'text' => (string) session('error')] : null,
        session()->has('warning') && filled(session('warning')) ? ['variant' => 'warning', 'text' => (string) session('warning')] : null,
        session()->has('info') && filled(session('info')) ? ['variant' => 'info', 'text' => (string) session('info')] : null,
    ])->filter()->values();
@endphp

@if ($flashToasts->isNotEmpty())
    <script>
        (() => {
            const toasts = @json($flashToasts);
            let hasShown = false;

            const showToasts = () => {
                if (hasShown) {
                    return true;
                }

                const api = window.Flux ?? window.$flux;

                if (api && typeof api.toast === 'function') {
                    toasts.forEach((toast) => api.toast(toast));
                    hasShown = true;
                    return true;
                }

                return false;
            };

            const waitAndShowToasts = () => {
                let attempts = 0;
                const maxAttempts = 40;

                const tryShow = () => {
                    if (showToasts()) {
                        return;
                    }

                    attempts += 1;

                    if (attempts < maxAttempts) {
                        setTimeout(tryShow, 50);
                    }
                };

                tryShow();
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', waitAndShowToasts, { once: true });
            } else {
                waitAndShowToasts();
            }

            document.addEventListener('livewire:navigated', waitAndShowToasts);
        })();
    </script>
@endif
