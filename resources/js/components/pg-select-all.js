window.pgAlpine.data('pgSelectAll', () => ({
    init() {
        this.pgTable = this.$el.closest('table')

        this.onRowChange = (event) => {
            const target = event.target

            if (target === this.$el || !(target instanceof HTMLInputElement) || target.type !== 'checkbox') {
                return
            }

            this.refresh()
        }

        this.pgTable?.addEventListener('change', this.onRowChange)

        this.onFinished = () => this.refresh()
        window.addEventListener('pg-select-all:refresh', this.onFinished)

        this.$nextTick(() => this.refresh())
    },

    destroy() {
        this.pgTable?.removeEventListener('change', this.onRowChange)
        window.removeEventListener('pg-select-all:refresh', this.onFinished)
    },

    refresh() {
        const table = this.pgTable

        if (!table) {
            return
        }

        const boxes = Array.from(table.querySelectorAll('tbody input[type="checkbox"]'))
            .filter((checkbox) => !checkbox.disabled)

        const total = boxes.length
        const checked = boxes.filter((checkbox) => checkbox.checked).length

        this.$el.checked = total > 0 && checked === total
        this.$el.indeterminate = checked > 0 && checked < total
    },
}))

if (!window.pgSelectAllCommitHook) {
    window.pgSelectAllCommitHook = true

    document.addEventListener('DOMContentLoaded', () => {
        if (!window.Livewire) {
            return
        }

        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                requestAnimationFrame(() => {
                    window.dispatchEvent(new CustomEvent('pg-select-all:refresh'))
                })
            })
        })
    })
}
