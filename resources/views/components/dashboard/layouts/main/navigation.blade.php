@use(\App\Models\Store)
@use(\App\Models\User)
@use(\App\Models\Template)
@use(\App\Models\Contact)
@use(\App\Models\Message)
@use(\App\Models\Setting)

<x-dashboard.layouts.main.nav-link
    :url="route(name: 'dashboard.home')"
    :text="__(key: 'dashboard.pages.home.title')"
    :is-active="request()->routeIs(patterns: 'dashboard.home')"
/>

@can('viewAny', Store::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.stores.index')"
        :text="__(key: 'dashboard.pages.stores.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.stores.*')"
    />
@endcan

@can('viewAnyEmployee', User::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.employees.index')"
        :text="__(key: 'dashboard.pages.employees.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.employees.*')"
    />
@endcan

@can('viewAny', Template::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.templates.index')"
        :text="__(key: 'dashboard.pages.templates.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.templates.*')"
    />
@endcan

@can('viewAny', Contact::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.contacts.index')"
        :text="__(key: 'dashboard.pages.contacts.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.contacts.*')"
    />
@endcan

@can('viewAny', Message::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.messages.index')"
        :text="__(key: 'dashboard.pages.messages.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.messages.*')"
    />
@endcan

@can('viewAny', Setting::class)
    <x-dashboard.layouts.main.nav-link
        :url="route(name: 'dashboard.settings.index')"
        :text="__(key: 'dashboard.pages.settings.index.title')"
        :is-active="request()->routeIs(patterns: 'dashboard.settings.*')"
    />
@endcan

