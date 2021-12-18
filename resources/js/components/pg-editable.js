export default (params) => ({
    editable: false,
    id: params.id ?? null,
    dataField: params.dataField ?? null,
    content: params.content,
    save() {
        document.getElementsByClassName('message')[0].style.display = "none";

        window.livewire.emit('pg:eventInputChanged', {
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
