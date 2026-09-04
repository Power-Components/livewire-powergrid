window.pgStoreMultiSelect = (params, value) => {
    Livewire.dispatch('pg:multiSelect-' + params.tableName, {
        label: params.label,
        field: params.dataField,
        values: value
    })
}
