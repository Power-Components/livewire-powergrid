export default (params) => ({
    dataField: params.dataField ?? null,
    tableName: params.tableName ?? null,
    initialValues: params.initialValues ?? [],
    defaultFramework: params.defaultFramework ?? [],
    init() {
        const element = '[x-ref="select_picker_'+this.dataField+'"]';

        if (window.TomSelect) {
            this.tomSelect(element)
        }

        if (window.SlimSelect) {
            this.slimSelect(element)
        }
    },
    slimSelect(element) {
        const select = new window.SlimSelect({
            select: element,
            ...this.defaultFramework,
            events: {
                afterChange: (newVal) => {
                    const arrSelected = [];

                    newVal.forEach(function (selected) {
                        arrSelected.push(selected.value);
                    });

                    window.livewire.emit('pg:multiSelect-' + this.tableName, {
                        id: this.dataField,
                        values: arrSelected
                    })

                    console.log(arrSelected)
                }
            }
        })

        select.setSelected(this.initialValues)
    },
    tomSelect (element) {
        new window.TomSelect(element,{
            items: this.initialValues,
            ...this.defaultFramework,
            onChange: (value) => {
                window.livewire.emit('pg:multiSelect-' + this.tableName, {
                    id: this.dataField,
                    values: value
                })
            },
        });
    }
})
