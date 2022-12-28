const emit = (listener, params, value) => {
    window.livewire.emit(listener + '-' + params.tableName, {
        id: params.dataField,
        values: value
    })
}

const storeMultiSelect = (params, value) => {
    emit('pg:multiSelect', params, value)
}

export { storeMultiSelect }
