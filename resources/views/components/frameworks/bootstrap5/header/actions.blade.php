@php
    $actionController = new \PowerComponents\LivewirePowerGrid\Components\Actions\ActionsController($this, collect());
    $headers = $this->header();
    $actions = $actionController->execute($headers);
@endphp
<div>
    @foreach ($actions as $action)
        <div @class(['me-1' => $loop->last, 'btn-group'])>
            {!! $action !!}
        </div>
    @endforeach
</div>
