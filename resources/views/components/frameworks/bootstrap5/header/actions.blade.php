<div>
    @foreach($headers as $action)
    <div @class(['me-1' => $loop->last, 'btn-group'])>
        @includeWhen($action->can, 'livewire-powergrid::components.actions-header', [
            'action' => $action
        ])
    </div>
    @endforeach
</div>
