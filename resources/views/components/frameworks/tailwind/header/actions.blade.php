@php
    $actionController = new \PowerComponents\LivewirePowerGrid\Components\Actions\ActionsController($this, collect());
    $headers = $this->header();
    $actions = $actionController->execute($headers);
@endphp
<div>
    @if (filled($actions))
        <div class="w-full md:w-auto">
            <div class="flex flex-wrap gap-2 mr-2">
                @foreach ($actions as $action)
                    <div class="w-full min-[370px]:!w-[calc(50%-4px)] sm:!w-[calc(33%-4px)] md:!w-auto">
                        {!! $action !!}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
