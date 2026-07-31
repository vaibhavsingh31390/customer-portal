@props(['title' => 'No data found', 'description' => 'Try adjusting your search or filters.', 'icon' => 'inbox'])

<div {{ $attributes->merge(['class' => 'portal-empty-state']) }}>
    <div class="portal-empty-state__icon">
        <x-icon :name="$icon" :size="40" />
    </div>
    <h3 class="portal-empty-state__title">{{ $title }}</h3>
    <p class="portal-empty-state__desc">{{ $description }}</p>
    @isset($action)
        <div class="portal-empty-state__action">{{ $action }}</div>
    @endisset
</div>
