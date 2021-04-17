<div class="dt--top-section">
    <div class="row">
        <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">

            <button class="btn livewire-powergrid" wire:click="exportToExcel()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-file-earmark-excel" viewBox="0 0 16 16">
                    <path
                        d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                    <path
                        d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                @if(count($checkbox_values) == 0)
                    {{ trans('livewire-powergrid::datatable.buttons.export') }}
                @elseif(count($checkbox_values) == 1)
                    {{ trans('livewire-powergrid::datatable.buttons.export_one') }}
                @else
                    {{ trans('livewire-powergrid::datatable.buttons.export_selected') }}
                @endif

            </button>
            <div class="d-flex align-items-center" style="padding-left: 10px;">
                <div wire:loading class="spinner-border ms-auto" role="status" aria-hidden="true" style="color: #656363;"></div>
            </div>

        </div>
        <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">
            <div class="col-12" style="text-align: right;">
                <label class="col-12 col-sm-8">
                    @if($search_input)
                        <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input wire:model.debounce.300ms="search" type="text"
                                   class="col-12 col-sm-8 form-control livewire_powergrid_input"
                                   placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">
                        </div>
                    @endif
                </label>
            </div>
        </div>
    </div>
</div>

