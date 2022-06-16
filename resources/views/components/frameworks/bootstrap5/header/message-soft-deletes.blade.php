@if(data_get($setUp, 'header.showMessageSoftDeletes') && ($softDeletes === 'withTrashed' || $softDeletes === 'onlyTrashed'))
    <div class="alert alert-warning my-1" role="alert">
        @if($softDeletes === 'withTrashed')
            @lang('livewire-powergrid::datatable.soft_deletes.message_with_trashed')
        @else
            @lang('livewire-powergrid::datatable.soft_deletes.message_only_trashed')
        @endif
    </div>
@endif