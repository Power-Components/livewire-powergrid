export default (params) => ({
    field: params.field ?? null,
    tableName: params.tableName ?? null,
    enabled: params.enabled ?? false,
    id: params.id,
    trueValue: params.trueValue,
    falseValue: params.falseValue,
    toggle: params.toggle,
    save() {
        const value = (this.toggle === 0 ? this.toggle = 1 : this.toggle = 0)

        document.getElementsByClassName('message')[0].style.display = "none";

        this.$wire.emit('pg:toggleable-' + this.tableName, {
            id: this.id,
            field: this.field,
            value: value
        })
    }
})
