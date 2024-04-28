@use(\App\Models\User)

<x-dashboard.nav-link
    :url="route(name: 'dashboard.home')"
    :text="__(key: 'dashboard.pages.home.title')"
    :is-active="request()->routeIs(patterns: 'dashboard.home')"
/>

@can('viewAny', User::class)
    <x-dashboard.nav-link
        :url="route(name: 'dashboard.employees.index')"
        :text="__(key: 'dashboard.pages.employees.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.employees.*')"
    />
@endcan

