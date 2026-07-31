@props(['label', 'value' => null, 'variant' => 'primary', 'icon' => null, 'valueId' => null])

<div {{ $attributes->merge(['class' => 'portal-stat-card portal-stat-card--' . $variant]) }}>
    <div class="portal-stat-card__content">
        <p class="portal-stat-card__label">{{ $label }}</p>
        <h3 class="portal-stat-card__value" @if($valueId) id="{{ $valueId }}" @endif>
            @if(isset($value)){{ $value }}@else{{ $slot }}@endif
        </h3>
    </div>
    @if ($icon)
        <div class="portal-stat-card__icon">
            <x-icon :name="$icon" :size="28" />
        </div>
    @endif
</div>
