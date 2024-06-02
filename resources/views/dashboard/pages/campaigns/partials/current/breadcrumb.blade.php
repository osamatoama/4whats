<x-dashboard.breadcrumb.breadcrumb>
    <x-dashboard.breadcrumb.link :url="route('dashboard.home')" :text="__(key: 'dashboard.common.dashboard')"/>
    <x-dashboard.breadcrumb.active :text="__(key: 'dashboard.pages.campaigns.title')"/>
    <x-dashboard.breadcrumb.active :text="__(key: 'dashboard.pages.campaigns.current.title')"/>
</x-dashboard.breadcrumb.breadcrumb>
