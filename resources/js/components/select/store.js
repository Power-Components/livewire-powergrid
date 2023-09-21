const dispatch = (listener, params, value) => {
    Livewire.dispatch(`${listener}-${params.tableName}`, {
        label: params.label,
        field: params.dataField,
        values: value
    })
}

const storeMultiSelect = (params, value) => {
    dispatch('pg:multiSelect', params, value)
}

export { storeMultiSelect }
