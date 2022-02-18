@props([
    'theme' => null
])
<div>
    @if ($this->persistFiltersAndColumns)
    <div x-data="{
         filters: $persist(@entangle('filters')).as('{{$this->tableName}}-filters'),
         enabledFilters: $persist(@entangle('enabledFilters')).as('{{$this->tableName}}-enabledFilters'),
         columns: $persist(@entangle('columns')).as('{{$this->tableName}}-columns'),
         }">
    </div>
    @endif
    <table class="table power-grid-table {{ $theme->tableClass }}"
           style="{{$theme->tableStyle}}">
        <thead class="{{$theme->theadClass}}"
               style="{{$theme->theadStyle}}">
                {{ $header }}
        </thead>
        <tbody class="{{$theme->tbodyClass}}"
               style="{{$theme->tbodyStyle}}">
                {{ $rows }}
        </tbody>
    </table>
</div>
