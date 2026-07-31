@props(['type' => 'info', 'title' => null])

<div {{ $attributes->merge(['class' => 'portal-alert portal-alert--' . $type, 'role' => 'alert']) }}>
    @if ($title)<strong class="portal-alert__title">{{ $title }}</strong>@endif
    <div class="portal-alert__body">{{ $slot }}</div>
</div>
