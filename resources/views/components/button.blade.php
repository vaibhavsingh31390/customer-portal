@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'loading' => false,
])

@php
    $classes = 'portal-btn btn btn-' . $variant . ($loading ? ' is-loading' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" size="18" class="portal-btn__icon" />@endif
        <span class="portal-btn__label">{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" size="18" class="portal-btn__icon" />@endif
        <span class="portal-btn__label">{{ $slot }}</span>
        <span class="portal-dots-loader portal-btn__spinner loader-btn" aria-hidden="true"><span></span><span></span><span></span></span>
    </button>
@endif
