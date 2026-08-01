@include('include.sidebarNav', [
    'items' => array_merge([
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'pattern' => 'dashboard'],
        ['route' => 'complaint', 'label' => 'Complaints', 'icon' => 'inbox', 'pattern' => 'complaint'],
    ], config('test.enabled') ? [
        ['route' => 'admin.users', 'label' => 'User Management', 'icon' => 'users', 'pattern' => 'admin.users'],
    ] : []),
])
