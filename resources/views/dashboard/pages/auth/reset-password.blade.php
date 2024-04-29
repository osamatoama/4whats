<x-dashboard.layouts.auth.layout>
    <x-slot:title>
        @lang('dashboard.pages.auth.reset_password.title')
    </x-slot:title>

    <x-slot:header>
        @include('dashboard.pages.auth.partials.reset-password.header')
    </x-slot:header>

    @include('dashboard.pages.auth.partials.reset-password.content')
</x-dashboard.layouts.auth.layout>
