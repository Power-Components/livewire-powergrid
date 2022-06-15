@if(data_get($setUp, 'header.showMessageSoftDeletes') && ($softDeletes === 'withTrashed' || $softDeletes === 'onlyTrashed'))
    <div class="alert alert-warning my-1" role="alert">
        @if($softDeletes === 'withTrashed')
            {{ trans('Exibindo todos registros, incluindo os excluídos.') }}
        @else
            {{ trans('Exibindo apenas os registros excluídos.') }}
        @endif
    </div>
@endif