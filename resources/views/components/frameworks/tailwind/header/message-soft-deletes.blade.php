@if(data_get($setUp, 'header.showMessageSoftDeletes') && ($softDeletes === 'withTrashed' || $softDeletes === 'onlyTrashed'))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-2 my-2">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    @if($softDeletes === 'withTrashed')
                        {{ trans('Exibindo todos registros, incluindo os excluídos.') }}
                    @else
                        {{ trans('Exibindo apenas os registros excluídos.') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
@endif
