@include('include.sidebarNav', [
    'items' => [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'pattern' => 'dashboard'],
        ['route' => 'complaint', 'label' => 'Complaint List', 'icon' => 'inbox', 'pattern' => 'complaint'],
        ['route' => 'register.complaint', 'label' => 'Complaint Register', 'icon' => 'clipboard-list', 'pattern' => 'register.complaint'],
    ],
])
