@props(['title' => null, 'class' => ''])

<div {{ $attributes->merge(['class' => 'portal-card card mb-30 ' . $class]) }}>
    <div class="portal-card__body card-body">
        @if ($title || isset($header))
            <div class="portal-card__header card-header">
                @if ($title)
                    <h5 class="portal-card__title card-title">{{ $title }}</h5>
                @endif
                @isset($header){{ $header }}@endisset
            </div>
        @endif
        {{ $slot }}
        @isset($footer)
            <div class="portal-card__footer">{{ $footer }}</div>
        @endisset
    </div>
</div>
