<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.campaigns.current.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.campaigns.current.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.campaigns.partials.current.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.campaigns.partials.current.content')
</x-dashboard.layouts.main.layout>
