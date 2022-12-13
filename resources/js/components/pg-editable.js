export default (params) => ({
    theme: params.theme,
    editable: false,
    tableName: params.tableName ?? null,
    id: params.id ?? null,
    dataField: params.dataField ?? null,
    content: params.content,
    oldContent: null,
    fallback: params.fallback,
    hash: null,
    hashError: true,
    showEditable: false,
    init() {
        if (this.content.length === 0 && this.fallback) {
            this.content = this.htmlSpecialChars(this.fallback);
        }

        this.hash = this.dataField + '-' + this.id

        this.$watch('editable', (value) => {
            if (value) {
                let showEditable = false

                this.showEditable = false

                this.content = this.htmlSpecialChars(this.content);

                this.oldContent = this.content;

                const editablePending = window.editablePending.notContains(this.hash)

                this.hashError = editablePending

                if (editablePending) {
                    const pendingHash = window.editablePending.pending[0]
                    document.getElementById('clickable-' + pendingHash).click()
                } else {
                    showEditable = true
                }

                this.$nextTick(() => setTimeout(() => {
                    this.showEditable = showEditable
                    this.focus()
                }, 150))
            }
        })

        this.content = this.htmlSpecialChars(this.content);
    },
    save() {
        if(this.$el.textContent == this.oldContent) {
            this.editable = false;
            this.showEditable = false;

            return;
        }

        setTimeout(() => {
            window.addEventListener('pg:editable-close-'+this.id, () => {
                window.editablePending.clear()
                this.editable = false;
                this.showEditable = false;
            })

            if(!window.editablePending.has(this.hash)) {
                window.editablePending.set(this.hash)
            }

            this.$wire.emit('pg:editable-' + this.tableName, {
                id: this.id,
                value: this.$el.textContent,
                field: this.dataField
            })

            this.oldContent = null

            this.$nextTick(() => setTimeout(() => {
                this.focus()
                setTimeout(() => this.$refs.editable.value = '', 200)
            }, 100))

        }, 100)

        this.content = this.htmlSpecialChars(this.$el.textContent)
    },
    focus() {
        const selection = window.getSelection();
        const range = document.createRange();
        selection.removeAllRanges();
        range.selectNodeContents(this.$refs.editable);
        range.collapse(false);
        selection.addRange(range);
        this.$refs.editable.focus();
    },
    cancel() {
        this.$refs.editable.textContent = this.oldContent;
        this.content = this.oldContent;
        this.editable = false;
        this.showEditable = false;
    },
    htmlSpecialChars(string) {
        const el = document.createElement('div');
        el.innerText = string;
        return el.innerHTML;
    }
})
