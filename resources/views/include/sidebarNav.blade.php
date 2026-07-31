@php
    $items = $items ?? [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'pattern' => 'dashboard'],
        ['route' => 'register.complaint', 'label' => 'Complaint Register', 'icon' => 'clipboard-list', 'pattern' => 'register.complaint'],
    ];
@endphp

<aside class="portal-sidebar-wrap" id="portal-sidebar" aria-label="Sidebar">
    <nav class="portal-sidebar">
        <div class="portal-sidebar__nav">
            @foreach ($items as $item)
                <a class="portal-sidebar__link {{ Request::routeIs($item['pattern']) ? 'active' : '' }}"
                    href="{{ route($item['route']) }}">
                    <x-icon :name="$item['icon']" :size="20" class="portal-sidebar__icon" />
                    <span class="portal-sidebar__label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</aside>
<div class="portal-sidebar-backdrop" id="portal-sidebar-backdrop"></div>
