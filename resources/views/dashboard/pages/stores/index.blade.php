<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.stores.index.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.stores.index.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.stores.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.stores.partials.index.content')
</x-dashboard.layouts.main.layout>
