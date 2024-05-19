<x-dashboard.layouts.main.layout>
    <x-slot:title>
        @lang('dashboard.pages.contacts.index.title')
    </x-slot:title>

    <x-slot:pageTitle>
        @lang('dashboard.pages.contacts.index.page_title')
    </x-slot:pageTitle>

    <x-slot:breadcrumb>
        @include('dashboard.pages.contacts.partials.index.breadcrumb')
    </x-slot:breadcrumb>

    @include('dashboard.pages.contacts.partials.index.content')
</x-dashboard.layouts.main.layout>
