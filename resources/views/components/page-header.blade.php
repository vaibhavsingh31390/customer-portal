@props(['title', 'subtitle' => null])

<header {{ $attributes->merge(['class' => 'portal-page-header main-content-header']) }}>
    <div class="portal-page-header__text">
        <h1 class="portal-page-header__title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="portal-page-header__subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="portal-page-header__actions action-btn">
            {{ $actions }}
        </div>
    @endif
</header>
