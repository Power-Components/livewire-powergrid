export default (params) => ({
    dataField: params.dataField ?? null,
    tableName: params.tableName ?? null,
    initialValues: params.initialValues ?? [],
    framework: params.framework ?? [],
    init() {
        const element = '[x-ref="select_picker_'+this.dataField+'"]';

        if (this.framework.default === 'tom') {
            this.tomSelect(element)
        }

        if (this.framework.default === 'slim') {
            this.slimSelect(element)
        }
    },
    slimSelect(element) {
        const select = new window.SlimSelect({
            select: element,
            ...this.framework,
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
                }
            }
        })

        select.setSelected(this.initialValues)
    },
    tomSelect (element) {
        new window.TomSelect(element,{
            items: this.initialValues,
            ...this.framework,
            onChange: (value) => {
                window.livewire.emit('pg:multiSelect-' + this.tableName, {
                    id: this.dataField,
                    values: value
                })
            },
        });
    }
})
