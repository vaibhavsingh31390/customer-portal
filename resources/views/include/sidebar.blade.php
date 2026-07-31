@include('include.sidebarNav', [
    'items' => [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'pattern' => 'dashboard'],
        ['route' => 'register.complaint', 'label' => 'Complaint Register', 'icon' => 'clipboard-list', 'pattern' => 'register.complaint'],
    ],
])
