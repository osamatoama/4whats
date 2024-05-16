<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.templates.index.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.templates.index.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.templates.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.templates.partials.index.content')
</x-dashboard.layouts.main.layout>
