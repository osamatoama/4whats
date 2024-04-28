<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.home.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.home.title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.home.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.home.partials.index.content')
</x-dashboard.layouts.main.layout>
