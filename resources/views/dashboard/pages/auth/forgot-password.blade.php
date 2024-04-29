<x-dashboard.layouts.auth.layout>
    <x-slot:title>
        @lang('dashboard.pages.auth.forgot_password.title')
    </x-slot:title>

    <x-slot:header>
        @include('dashboard.pages.auth.partials.forgot-password.header')
    </x-slot:header>

    @include('dashboard.pages.auth.partials.forgot-password.content')
</x-dashboard.layouts.auth.layout>
