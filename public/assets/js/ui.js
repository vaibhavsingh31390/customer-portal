/**
 * MAWAI Customer Portal — UI interactions
 * Theme toggle, NProgress, skeleton loaders, Lucide icons, button loading.
 */
(function ($) {
    'use strict';

    /* ------------------------------------------------------------------ */
    /* Lucide icons                                                        */
    /* ------------------------------------------------------------------ */
    function initLucide() {
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }

    /* ------------------------------------------------------------------ */
    /* Theme toggle                                                        */
    /* ------------------------------------------------------------------ */
    function getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function getTheme() {
        return document.documentElement.getAttribute('data-theme') || getSystemTheme();
    }

    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        updateThemeToggleIcon(theme);
        initLucide();
    }

    function toggleTheme() {
        setTheme(getTheme() === 'dark' ? 'light' : 'dark');
    }

    function updateThemeToggleIcon(theme) {
        var btn = document.getElementById('theme-toggle');
        if (!btn) return;
        var icon = btn.querySelector('[data-lucide]');
        if (!icon) return;
        icon.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
        btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        initLucide();
        document.dispatchEvent(new CustomEvent('portal:theme-change', { detail: { theme: theme } }));
    }

    function updateSidebarToggleIcon(collapsed) {
        var btn = document.getElementById('sidebar-toggle');
        if (!btn) return;
        var icon = btn.querySelector('[data-lucide]');
        if (!icon) return;
        var isMobile = window.innerWidth < 1200;
        var iconName = (isMobile && !collapsed) ? 'x' : 'menu';
        icon.setAttribute('data-lucide', iconName);
        btn.setAttribute('aria-expanded', isMobile ? String(!collapsed) : 'true');
        initLucide();
    }

    var registeredCharts = [];

    function registerChart(chart, getOptions) {
        if (!chart || typeof getOptions !== 'function') return;
        registeredCharts.push({ chart: chart, getOptions: getOptions });
    }

    document.addEventListener('portal:theme-change', function () {
        registeredCharts.forEach(function (entry) {
            if (entry.chart && entry.chart.updateOptions) {
                entry.chart.updateOptions(entry.getOptions());
            }
        });
    });

    $(document).on('click', '#theme-toggle', function (e) {
        e.preventDefault();
        toggleTheme();
    });

    /* ------------------------------------------------------------------ */
    /* Select2 — full-width portal selects                                 */
    /* ------------------------------------------------------------------ */
    function initSelect2(selector) {
        if (!$.fn.select2) {
            return;
        }

        var $elements = selector ? $(selector) : $('.portal-select2');

        $elements.each(function () {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                return;
            }

            var placeholder = $el.data('placeholder') || '';
            var hasEmptyOption = $el.find('option[value=""]').length > 0;

            $el.select2({
                width: '100%',
                allowClear: hasEmptyOption,
                placeholder: placeholder || (hasEmptyOption ? '' : undefined),
                dropdownAutoWidth: false,
            });

            $el.next('.select2-container').css('width', '100%');
        });
    }

    /* ------------------------------------------------------------------ */
    /* NProgress-style top bar                                             */
    /* ------------------------------------------------------------------ */
    var nprogress = {
        bar: null,
        ensure: function () {
            if (!this.bar) {
                this.bar = $('<div id="nprogress-bar" class="nprogress-bar" role="progressbar"></div>');
                $('body').prepend(this.bar);
            }
        },
        start: function () {
            this.ensure();
            this.bar.addClass('is-active');
        },
        done: function () {
            if (this.bar) {
                this.bar.removeClass('is-active');
            }
        }
    };

    $(document).ajaxStart(function () {
        nprogress.start();
    }).ajaxStop(function () {
        nprogress.done();
    });

    /* ------------------------------------------------------------------ */
    /* Skeleton table loaders                                              */
    /* ------------------------------------------------------------------ */
    function showTableSkeleton() {
        $('.loader-wrapper, .portal-table-loader').css('display', 'flex').attr('aria-busy', 'true');
        $('.portal-table-loader__status').show();
        $('.loader-wrapper .portal-skeleton-table--block, .portal-table-loader .portal-skeleton-table--block').show();
        $('.portal-table-wrap, .table-responsive.portal-table-wrap').css('display', 'none');
    }

    function hideTableSkeleton() {
        $('.loader-wrapper, .portal-table-loader').css('display', 'none').attr('aria-busy', 'false');
        $('.portal-table-wrap, .table-responsive.portal-table-wrap').css('display', 'block');
    }

    /* Override default ajax handlers when portal-table-wrap present */
    $(document).ajaxStart(function () {
        if ($('.portal-table-wrap').length) {
            showTableSkeleton();
        }
    }).ajaxStop(function () {
        if ($('.portal-table-wrap').length) {
            hideTableSkeleton();
        }
    });

    /* ------------------------------------------------------------------ */
    /* Button loading on form submit                                       */
    /* ------------------------------------------------------------------ */
    $(document).on('click', '#submitComplaint, #executeComplaintRegister', function () {
        var $btn = $(this);
        $btn.addClass('is-loading').prop('disabled', true);
        $btn.find('.loader-btn').css('display', 'inline-block');
        var label = $btn.find('.portal-btn__label');
        if (label.length && !$btn.data('original-text')) {
            $btn.data('original-text', label.text());
            label.text('Saving…');
        }
    });

    $(document).ajaxStop(function () {
        $('#submitComplaint, #executeComplaintRegister').each(function () {
            var $btn = $(this);
            if ($btn.hasClass('is-loading')) {
                $btn.removeClass('is-loading').prop('disabled', false);
                $btn.find('.loader-btn').css('display', 'none');
                var original = $btn.data('original-text');
                if (original) {
                    $btn.find('.portal-btn__label').text(original);
                }
            }
        });
    });

    /* ------------------------------------------------------------------ */
    /* Login form submit loading                                           */
    /* ------------------------------------------------------------------ */
    $('#login-form').on('submit', function () {
        var $btn = $(this).find('button[type="submit"]');
        $btn.addClass('is-loading').prop('disabled', true);
        if (!$btn.find('.portal-btn__spinner').length) {
            $btn.append('<span class="portal-dots-loader portal-btn__spinner loader-btn" aria-hidden="true"><span></span><span></span><span></span></span>');
        }
        $btn.find('.portal-btn__label').text('Signing in…');
    });

    /* ------------------------------------------------------------------ */
    /* Init on ready                                                       */
    /* ------------------------------------------------------------------ */
    $(document).ready(function () {
        initLucide();
        updateThemeToggleIcon(getTheme());
        initSelect2('.portal-select2');

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    });

    window.PortalUI = {
        setTheme: setTheme,
        getTheme: getTheme,
        initLucide: initLucide,
        nprogress: nprogress,
        chartColors: function () {
            var s = getComputedStyle(document.documentElement);
            return {
                primary: s.getPropertyValue('--color-primary').trim(),
                success: s.getPropertyValue('--color-success').trim(),
                text: s.getPropertyValue('--color-text-muted').trim(),
                border: s.getPropertyValue('--color-border').trim(),
                surface: s.getPropertyValue('--color-surface').trim(),
            };
        },
        complaintChartOptions: function (series, categories) {
            var colors = this.chartColors();
            var isDark = getTheme() === 'dark';
            return {
                chart: {
                    height: 360,
                    type: 'bar',
                    toolbar: { show: false },
                    background: 'transparent',
                    foreColor: colors.text,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6,
                        columnWidth: '42%',
                    },
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '12px', colors: [colors.text] },
                    formatter: function (val) { return Math.round(val); },
                },
                colors: [colors.primary, colors.success],
                series: series,
                xaxis: {
                    categories: categories || ['Complaints'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: colors.text } },
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5,
                    labels: {
                        formatter: function (val) { return Math.round(val); },
                        style: { colors: colors.text },
                    },
                },
                grid: {
                    borderColor: colors.border,
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    labels: { colors: colors.text },
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'var(--font-sans)',
                    },
                },
                theme: { mode: isDark ? 'dark' : 'light' },
            };
        },
        statusChartOptions: function (dataPoints, labels, barColors) {
            var colors = this.chartColors();
            var isDark = getTheme() === 'dark';
            var seriesData = dataPoints || [];
            var categories = labels || [];
            var distributedColors = barColors && barColors.length
                ? barColors
                : [colors.primary, colors.success, '#2563eb', '#16a34a', '#dc2626'];

            return {
                chart: {
                    height: 360,
                    type: 'bar',
                    toolbar: { show: false },
                    background: 'transparent',
                    foreColor: colors.text,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6,
                        columnWidth: '55%',
                        distributed: true,
                    },
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '12px', colors: [colors.text] },
                    formatter: function (val) { return Math.round(val); },
                },
                colors: distributedColors,
                series: [{ name: 'Complaints', data: seriesData }],
                xaxis: {
                    categories: categories,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: colors.text, fontSize: '11px' },
                        rotate: -25,
                        trim: true,
                    },
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5,
                    labels: {
                        formatter: function (val) { return Math.round(val); },
                        style: { colors: colors.text },
                    },
                },
                grid: {
                    borderColor: colors.border,
                    strokeDashArray: 4,
                },
                legend: { show: false },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'var(--font-sans)',
                    },
                },
                theme: { mode: isDark ? 'dark' : 'light' },
            };
        },
        updateSidebarToggleIcon: updateSidebarToggleIcon,
        registerChart: registerChart,
        initSelect2: initSelect2,
        showTableSkeleton: showTableSkeleton,
        hideTableSkeleton: hideTableSkeleton,
    };
})(jQuery);
