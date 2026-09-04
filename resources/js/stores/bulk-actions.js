window.pgAlpine.store('pgBulkActions', {
    selected: {},

    init() {
        window.addEventListener('pgBulkActions::addMore', (event) => {
            const params = livewireEventPayload(event)

            if (!params || typeof params !== 'object' || !params.tableName) {
                return
            }

            if (typeof this.selected[params.tableName] === 'undefined') {
                this.selected[params.tableName] = []
            }

            this.selected[params.tableName].push(params.value)
        })

        window.addEventListener('pgBulkActions::clear', (event) => {
            const tableName = livewireEventPayload(event)

            if (typeof tableName === 'string') {
                this.clear(tableName)
            }
        })

        window.addEventListener('pgBulkActions::clearAll', () => {
            this.clearAll()
        })
    },

    add(value, tableName) {
        if (typeof this.selected[tableName] === 'undefined') {
            this.selected[tableName] = []
        }

        if (!this.selected[tableName].includes(value)) {
            this.selected[tableName].push(value)

            return
        }

        this.remove(value, tableName)
    },

    remove(value, tableName) {
        const index = this.selected[tableName].indexOf(value)
        if (index > -1) {
            this.selected[tableName].splice(index, 1)
        }
    },

    get(tableName) {
        return this.selected[tableName] ?? []
    },

    count(tableName) {
        if (typeof this.selected[tableName] === 'undefined') {
            return 0
        }

        return this.selected[tableName].length
    },

    clear(tableName) {
        this.selected[tableName] = []
    },

    clearAll() {
        this.selected = {}
    }
})

function livewireEventPayload(event) {
    const detail = event.detail

    if (Array.isArray(detail)) {
        return detail[0]
    }

    return detail
}
