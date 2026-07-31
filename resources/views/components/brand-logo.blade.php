@php
    $logoSrc = asset('assets/img/heyvai-dev-logo.png');
    $logoAlt = 'heyvai.dev';
@endphp

<img src="{{ $logoSrc }}" alt="{{ $logoAlt }}" {{ $attributes->merge(['class' => 'portal-brand-logo']) }}>
