@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
<div class="w-full md:w-auto">
    <div class="sm:flex sm:flex-row">
        @foreach($headers as $action)
            <div class="sm:mr-2 w-auto">
                @includeWhen($action->can, 'livewire-powergrid::components.actions-header', [
                    'action' => $action
                ])
            </div>
        @endforeach
    </div>
</div>
