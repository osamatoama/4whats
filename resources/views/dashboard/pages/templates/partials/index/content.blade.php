<div class="row">
    @foreach($templates as $template)
        <livewire:dashboard.templates.card :template="$template"/>
    @endforeach
    
    <livewire:dashboard.templates.order-statuses :templates="$orderStatusesTemplates"/>
</div>
