<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.messages.index.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.messages.index.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.messages.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.messages.partials.index.content')
</x-dashboard.layouts.main.layout>
