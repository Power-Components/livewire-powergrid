export default (params) => ({
    theme: params.theme,
    editable: false,
    tableName: params.tableName ?? null,
    id: params.id ?? null,
    dataField: params.dataField ?? null,
    content: params.content,
    oldContent: null,
    fallback: params.fallback,
    init() {
        if(this.content.length === 0 && this.fallback) {
            this.content = this.fallback
        }

        this.$watch('editable', (value) => {
            if(value) {
                this.oldContent = this.content;
                this.$nextTick(() => setTimeout(this.$refs.editable.focus(), 50))
            }
        })
    },
    save() {
        setTimeout(() => {
            this.$wire.emit('pg:editable-' + this.tableName, {
                id: this.id,
                value: this.$el.value,
                field: this.dataField
            })
        }, 200)

        this.editable = false;
        this.content  = this.htmlSpecialChars(this.$el.value)
    },
    cancel()
    {
        this.$refs.editable.value = this.oldContent;
        this.content = this.oldContent;
        this.editable = false;
    },
    htmlSpecialChars(string) {
        const el = document.createElement('div');
        el.innerText = string;
        return el.innerHTML;
    }
})
