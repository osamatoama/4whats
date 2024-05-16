@use(\App\Models\User)
@use(\App\Models\MessageTemplate)

<x-dashboard.layouts.main.nav-link
    :url="route(name: 'dashboard.home')"
    :text="__(key: 'dashboard.pages.home.title')"
    :is-active="request()->routeIs(patterns: 'dashboard.home')"
/>

@can('viewAny', User::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.employees.index')"
        :text="__(key: 'dashboard.pages.employees.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.employees.*')"
    />
@endcan

@can('viewAny', MessageTemplate::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.templates.index')"
        :text="__(key: 'dashboard.pages.templates.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.templates.*')"
    />
@endcan

