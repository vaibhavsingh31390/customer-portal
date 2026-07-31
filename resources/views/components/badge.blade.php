@props(['variant' => 'default'])

<span {{ $attributes->merge(['class' => 'portal-badge portal-badge--' . $variant]) }}>
    {{ $slot }}
</span>
