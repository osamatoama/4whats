@if(auth()->user()->is_admin)
    @include('dashboard.pages.settings.partials.index.content.admin')
@else
    @include('dashboard.pages.settings.partials.index.content.merchant')
@endif
