<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.settings.index.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.settings.index.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.settings.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.settings.partials.index.content')
</x-dashboard.layouts.main.layout>
