<x-dashboard.layouts.auth.layout>
    <x-slot:title>
        @lang('dashboard.pages.auth.login.title')
    </x-slot:title>

    <x-slot:header>
        @include('dashboard.pages.auth.partials.login.header')
    </x-slot:header>

    @include('dashboard.pages.auth.partials.login.content')
</x-dashboard.layouts.auth.layout>
