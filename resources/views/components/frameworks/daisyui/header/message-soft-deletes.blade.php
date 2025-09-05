@if (data_get($setUp, 'header.showMessageSoftDeletes') &&
        ($softDeletes === 'withTrashed' || $softDeletes === 'onlyTrashed'))
    <div class="bg-warning alert mb-3">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm text-warning-content">
                    @if ($softDeletes === 'withTrashed')
                        @lang('livewire-powergrid::datatable.soft_deletes.message_with_trashed')
                    @else
                        @lang('livewire-powergrid::datatable.soft_deletes.message_only_trashed')
                    @endif
                </p>
            </div>
        </div>
    </div>
@endif
