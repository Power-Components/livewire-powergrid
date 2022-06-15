@if($softDeletes === 'withTrashed' || $softDeletes === 'onlyTrashed' && data_get($setUp, 'header.showMessageSoftDeletes'))
    <div class="alert alert-warning" role="alert">
        @if($softDeletes === 'withTrashed')
            {{ trans('Exibindo todos registros, incluindo os excluídos.') }}
        @else
            {{ trans('Exibindo apenas os registros excluídos.') }}
        @endif
    </div>
@endif