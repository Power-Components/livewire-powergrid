<div>
    @if($exportOption)
        <div class="btn-group">
            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
            </button>
            <ul class="dropdown-menu">
                @if(in_array('excel',$exportType))
                    <li><a class="dropdown-item" wire:click="exportToXLS()" href="#">Excel</a></li>
                @endif
                @if(in_array('csv',$exportType))
                    <li><a class="dropdown-item" wire:click="exportToCsv()" href="#">Csv</a></li>
                @endif
            </ul>
        </div>
    @endif
</div>
