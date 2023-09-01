document.addEventListener('alpine:init', () => {
    window.Alpine.store('editablePending', {
        pending: [],
        set(value) {
            this.pending.push(value)
        },
        has(value) {
            return this.pending.includes(value)
        },
        notContains(value) {
            return this.pending.length > 0 && !this.pending.includes(value)
        },
        clear() {
            this.pending = []
        },
        isNotEmpty() {
            return this.pending.length > 0
        },
    });

    window.Alpine.store('pgBulkActions', {
        selected: [],
        init() {
            window.addEventListener('pgBulkActions::addMore', (event) => {
                const params = event.__livewire.params[0]

                if(typeof this.selected[params.tableName] == 'undefined') {
                    this.selected[params.tableName] = []
                }

                this.selected[params.tableName].push(params.value)
            })
            window.addEventListener('pgBulkActions::clear', (event) => {
                this.clear(event.detail);
            })
            window.addEventListener('pgBulkActions::clearAll', () => {
                this.clearAll();
            })
        },
        add(value, tableName) {
            if(typeof this.selected[tableName] == 'undefined') {
               this.selected[tableName] = []
            }

            if(!this.selected[tableName].includes(parseFloat(value))) {
                this.selected[tableName].push(parseFloat(value))

                return;
            }

            this.remove(parseFloat(value), tableName)
        },
        remove(value, tableName) {
            const index = this.selected[tableName].indexOf(value);
            if (index > -1) {
                this.selected[tableName].splice(index, 1);
            }
        },
        get(tableName) {
            return this.selected[tableName]
        },
        count(tableName) {
            if(typeof this.selected[tableName] == 'undefined') {
                return 0;
            }

            return this.selected[tableName].length
        },
        clear(tableName) {
            this.selected[tableName] = []
        },
        clearAll() {
            this.selected = []
        }
    })

    window.editablePending = window.Alpine.store('editablePending')
    window.pgBulkActions   = window.Alpine.store('pgBulkActions')
})
