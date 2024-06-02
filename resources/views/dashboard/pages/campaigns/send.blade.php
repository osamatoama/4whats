<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.campaigns.send.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.campaigns.send.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.campaigns.partials.send.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.campaigns.partials.send.content')
</x-dashboard.layouts.main.layout>
