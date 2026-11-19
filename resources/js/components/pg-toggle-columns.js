// Toggle-columns panel: the list DOM is the draft; apply() rearranges the table cells without a re-render.
window.pgAlpine.data('pgToggleColumns', (defaults = []) => ({
    open: false,
    hiddenCount: 0,

    init() {
        this.hiddenCount = this.draft().filter(column => column.hidden).length
    },

    items() {
        return [...this.$refs.list.querySelectorAll('[data-pg-toggle-item]')]
    },

    draft() {
        return this.items().map(item => ({
            field: item.dataset.field,
            column: item.dataset.column,
            hidden: !item.querySelector('input[type="checkbox"]').checked,
        }))
    },

    move(handle, offset) {
        const item = handle.closest('[data-pg-toggle-item]')
        const sibling = offset < 0 ? item.previousElementSibling : item.nextElementSibling

        if (!sibling) {
            return
        }

        offset < 0 ? sibling.before(item) : sibling.after(item)
        handle.focus()
    },

    reset() {
        const items = new Map(this.items().map(item => [item.dataset.field, item]))

        defaults.forEach(({ field, hidden }) => {
            const item = items.get(field)

            if (item) {
                this.$refs.list.append(item)
                item.querySelector('input[type="checkbox"]').checked = !hidden
            }
        })

        this.apply()
    },

    apply() {
        const draft = this.draft()
        const hidden = new Map(draft.map(column => [column.column, column.hidden]))
        const component = this.$root.closest('[wire\\:id]')

        component.querySelectorAll('tr').forEach(row => {
            // Skip rows of nested components (e.g. a table inside a detail row).
            if (row.closest('[wire\\:id]') !== component) {
                return
            }

            const cells = new Map()

            for (const cell of row.children) {
                if (hidden.has(cell.dataset.column) && !cells.has(cell.dataset.column)) {
                    cells.set(cell.dataset.column, cell)
                }
            }

            // Listed cells swap among their own slots; checkbox, detail and action cells stay put.
            const slots = [...cells.values()].map(cell => {
                const slot = document.createComment('')
                cell.replaceWith(slot)

                return slot
            })

            draft.filter(column => cells.has(column.column)).forEach((column, index) => {
                const cell = cells.get(column.column)
                cell.style.display = column.hidden ? 'none' : ''
                slots[index].replaceWith(cell)
            })
        })

        this.hiddenCount = draft.filter(column => column.hidden).length
        this.open = false
        this.$wire.applyColumns(draft.map(({ field, hidden }) => ({ field, hidden })))
    },
}))
