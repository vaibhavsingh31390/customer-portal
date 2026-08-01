@props([
    'variant' => 'inline',
    'label' => 'Loading',
    'size' => 'md',
])

@if ($variant === 'page')
    <div {{ $attributes->merge(['class' => 'preloader portal-preloader', 'id' => 'portal-preloader', 'aria-live' => 'polite', 'aria-busy' => 'true']) }}>
        <div class="portal-preloader__mesh" aria-hidden="true"></div>
        <div class="portal-preloader__card">
            <div class="portal-orbit-loader portal-orbit-loader--hero" aria-hidden="true">
                <span class="portal-orbit-loader__halo"></span>
                <span class="portal-orbit-loader__arc portal-orbit-loader__arc--primary"></span>
                <span class="portal-orbit-loader__arc portal-orbit-loader__arc--accent"></span>
                <span class="portal-orbit-loader__core">
                    <x-brand-logo class="portal-orbit-loader__logo" />
                </span>
            </div>
            <div class="portal-preloader__shimmer" aria-hidden="true"><span></span></div>
            <span class="portal-sr-only">{{ $label }}</span>
        </div>
    </div>
@elseif ($variant === 'table')
    <div {{ $attributes->merge(['class' => 'portal-table-loader__status']) }}>
        <div class="portal-orbit-loader portal-orbit-loader--md" aria-hidden="true">
            <span class="portal-orbit-loader__arc portal-orbit-loader__arc--primary"></span>
            <span class="portal-orbit-loader__arc portal-orbit-loader__arc--accent"></span>
            <span class="portal-orbit-loader__core portal-orbit-loader__core--dot"></span>
        </div>
        <span class="portal-table-loader__label portal-table-loader__label--pulse" aria-hidden="true">Fetching records</span>
        <span class="portal-sr-only">{{ $label }}</span>
    </div>
@elseif ($variant === 'button')
    <span {{ $attributes->merge(['class' => 'portal-dots-loader portal-btn__spinner loader-btn', 'aria-hidden' => 'true']) }}>
        <span></span><span></span><span></span>
    </span>
@else
    <span {{ $attributes->merge(['class' => 'portal-orbit-loader portal-orbit-loader--' . $size, 'aria-hidden' => 'true']) }}>
        <span class="portal-orbit-loader__arc portal-orbit-loader__arc--primary"></span>
        <span class="portal-orbit-loader__core portal-orbit-loader__core--dot"></span>
    </span>
@endif
