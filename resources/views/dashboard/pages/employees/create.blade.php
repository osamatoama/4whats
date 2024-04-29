<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.employees.create.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.employees.create.title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.employees.partials.create.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.employees.partials.create.content')
</x-dashboard.layouts.main.layout>
