export default (params) => ({
    editable: false,
    tableName: params.tableName ?? null,
    id: params.id ?? null,
    dataField: params.dataField ?? null,
    content: params.content,
    save() {
        this.$wire.emit('pg:editable-' + this.tableName, {
            id: this.id,
            value: this.$el.value,
            field: this.dataField
        })

        this.editable = false;
        this.content  = this.htmlSpecialChars(this.$el.value)
    },
    htmlSpecialChars(string) {
        const el = document.createElement('div');
        el.innerText = string;
        return el.innerHTML;
    }
})
