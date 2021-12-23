export default (params) => ({
    tableName: params.tableName ?? null,
    columnField: params.columnField ?? null,
    dataField: params.dataField ?? null,
    options: [],
    data: params.data,
    selected: [],
    show: false,
    init() {
        const options = this.data

        for (let i = 0; i < options.length; i++) {
            this.options.push({
                value: options[i].value.id,
                text: options[i].value.name,
                selected: false
            });
        }
    },
    selectedValues() {
        return this.selected.map((option) => {
            return this.options[option].value;
        })
    },
    select(index, event) {
        if (!this.options[index].selected) {
            this.options[index].selected = true;
            this.options[index].element = event.target;
            this.selected.push(index);
            this.show = false
            this.$wire.emit('pg:multiSelect-' + this.tableName, {
                id: this.dataField,
                values: this.selectedValues()
            });
        } else {
            this.selected.splice(this.selected.lastIndexOf(index), 1);
            this.options[index].selected = false
            this.show = false
        }
    },
    remove(index, option) {
        this.options[option].selected = false;
        this.selected.splice(index, 1);
        this.$wire.emit('pg:multiSelect-' + this.tableName, {
            id: this.dataField,
            values: this.selectedValues()
        });
    },
})
