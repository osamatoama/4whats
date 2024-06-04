<div class="row">
    @foreach($templates as $template)
        <livewire:dashboard.templates.card :template="$template"/>
    @endforeach

    @if($orderStatusesTemplates->isNotEmpty())
        <livewire:dashboard.templates.order-statuses :templates="$orderStatusesTemplates"/>
    @endif
</div>
