@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
<div class="w-full md:w-auto">
    <div class="flex flex-wrap gap-2 mr-2">
        @foreach($headers as $action)
            <div class="w-full min-[370px]:!w-[calc(50%-4px)] sm:!w-[calc(33%-4px)] md:!w-auto">
                @includeWhen($action->can, 'livewire-powergrid::components.actions-header', [
                    'action' => $action
                ])
            </div>
        @endforeach
    </div>
</div>
