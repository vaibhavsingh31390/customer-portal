@include('include.sidebarNav', [
    'items' => [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'pattern' => 'dashboard'],
        ['route' => 'complaint', 'label' => 'Complaints', 'icon' => 'inbox', 'pattern' => 'complaint'],
    ],
])
