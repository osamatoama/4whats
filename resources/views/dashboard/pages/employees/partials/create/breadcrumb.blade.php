<x-dashboard.breadcrumb.breadcrumb>
    <x-dashboard.breadcrumb.link :url="route('dashboard.home')" :text="__(key: 'dashboard.common.dashboard')"/>
    <x-dashboard.breadcrumb.link :url="route('dashboard.employees.index')" :text="__(key: 'dashboard.pages.employees.index.title')"/>
    <x-dashboard.breadcrumb.active :text="__(key: 'dashboard.pages.employees.create.title')"/>
</x-dashboard.breadcrumb.breadcrumb>
