export default (params) => ({
    theme: params.theme,
    id: params.id ?? null,
    dataField: params.dataField ?? null,
    content: params.content,
    fallback: params.fallback,
    inputClass: params.inputClass,
    saveOnMouseOut: params.saveOnMouseOut,
    oldContent: null,
    editable: false,
    hash: null,
    hashError: true,
    showEditable: false,
    editableInput: '',
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

                setTimeout(() => {
                    if (window.editablePending.getTextContent(this.hash) && document.getElementById('editable-' + this.hash)) {
                        document.getElementById('editable-' + this.hash).textContent =
                            window.editablePending.getTextContent(this.hash)
                    }
                }, 220)

                if (editablePending) {
                    const pendingHash = window.editablePending.get(this.hash)
                    document.getElementById('clickable-' + pendingHash).click()
                } else {
                    showEditable = true
                }

                this.editableInput = `
                <div
                    x-ref="editable"
                    x-text="content"
                    value="${this.content}"
                    placeholder="${this.content}"
                    contenteditable
                    class="pg-single-line ${this.inputClass}"
                    ${this.saveOnMouseOut ? 'x-on:mousedown.outside="save()"' : ''}
                    x-on:keydown.enter="save()"
                    id="${`editable-` + this.dataField + `-` + this.id}"
                    x-on:keydown.esc="cancel"
                >
                </div>`;

                this.$nextTick(() => setTimeout(() => {
                    this.showEditable = showEditable
                    this.focus()
                }, 150))
            }
        })

        this.content = this.htmlSpecialChars(this.content);
    },
    save() {
        window.editablePending.clear()
        window.editablePending.set(this.hash, this.$el.textContent)

        setTimeout(() => {
            document.getElementById('clickable-' + this.hash).textContent =
                this.$el.textContent
        }, 230)

        setTimeout(() => {
            window.addEventListener('pg:editable-close-'+this.id, () => {
                window.editablePending.clear()
                this.editable = false;
                this.showEditable = false;
            })

            if(!window.editablePending.has(this.hash)) {
                window.editablePending.set(this.hash, this.$el.textContent)

            }

            this.$wire.dispatch('pg:editable-' + this.$wire.tableName, {
                id: this.id,
                value: this.$el.textContent,
                field: this.dataField
            })

            this.oldContent = window.editablePending.getTextContent(this.hash)

            this.$nextTick(() => setTimeout(() => {
                this.focus()
                setTimeout(() => this.$el.setAttribute('value', ''), 200)
            }, 100))

        }, 100)

        this.content = this.htmlSpecialChars(this.$el.textContent)
    },
    focus() {
        const selection = window.getSelection();
        const range = document.createRange();
        selection.removeAllRanges();
        range.selectNodeContents(this.$el);
        range.collapse(false);
        selection.addRange(range);
        this.$el.focus();
    },
    cancel() {
        this.$refs.editable.textContent = this.oldContent;
        this.content = this.oldContent;
        this.editable = false;
        this.showEditable = false;
    },
    htmlSpecialChars(string) {
        const el = document.createElement('div');
        el.innerHTML = string;
        return el.textContent;
    }
})
