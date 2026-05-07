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

            const showToast = (toast) => {
                const api = window.Flux ?? window.$flux;

                if (api && typeof api.toast === 'function') {
                    api.toast(toast);
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => toasts.forEach(showToast), { once: true });
            } else {
                toasts.forEach(showToast);
            }
        })();
    </script>
@endif
