export default (params) => ({
    field: params.field ?? null,
    tableName: params.tableName ?? null,
    enabled: params.enabled ?? false,
    id: params.id,
    trueValue: params.trueValue,
    falseValue: params.falseValue,
    toggle: params.toggle,
    save() {
        this.toggle = (this.toggle === 0 ?  1 :  0);

        this.$wire.emit('pg:toggleable-' + this.tableName, {
            id: this.id,
            field: this.field,
            value: this.toggle
        })
    }
})
