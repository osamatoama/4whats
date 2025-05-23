<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.employees.index.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.employees.index.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.employees.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.employees.partials.index.content')
</x-dashboard.layouts.main.layout>
