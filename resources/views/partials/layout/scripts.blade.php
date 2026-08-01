<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="{{ asset('assets/js/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/viewer.min.js') }}"></script>

@if (!empty($loadApexCharts))
    <script src="{{ asset('assets/js/apex-charts/apexcharts.min.js') }}"></script>
@endif

<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('assets/js/ui.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/toastify-js.js') }}"></script>

<script>
    const showToast = (message, type = true, duration = 5000, position = 'right') => {
        const root = document.documentElement;
        const style = getComputedStyle(root);
        const bgColor = type
            ? style.getPropertyValue('--color-success-bg').trim()
            : style.getPropertyValue('--color-error-bg').trim();
        const textColor = type
            ? style.getPropertyValue('--color-toast-success-text').trim()
            : style.getPropertyValue('--color-toast-error-text').trim();

        Toastify({
            text: message,
            duration: duration,
            close: true,
            gravity: 'top',
            stopOnFocus: true,
            position: position,
            style: {
                background: bgColor,
                color: textColor,
            },
        }).showToast();
    };

    $(document).ready(function () {
        if (window.PortalUI && window.PortalUI.initLucide) {
            window.PortalUI.initLucide();
        }

        const storedToast = sessionStorage.getItem('portalToast');
        if (storedToast) {
            sessionStorage.removeItem('portalToast');
            try {
                const data = JSON.parse(storedToast);
                if (data.message) {
                    showToast(data.message, data.type !== false);
                }
            } catch (e) {
                /* ignore malformed toast payload */
            }
        }
    });

    window.redirectWithToast = function (url, message, success = true) {
        sessionStorage.setItem('portalToast', JSON.stringify({ message: message, type: success }));
        window.location.href = url;
    };

    window.navigateBackWithToast = function (message, success = true, fallbackUrl) {
        sessionStorage.setItem('portalToast', JSON.stringify({ message: message, type: success }));
        if (window.history.length > 1) {
            window.history.back();
        } else if (fallbackUrl) {
            window.location.href = fallbackUrl;
        }
    };
</script>

@yield('scripts')
@stack('scripts')
