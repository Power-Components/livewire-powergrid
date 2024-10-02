document.addEventListener('alpine:init', () => {
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

            if(!this.selected[tableName].includes(value)) {
                this.selected[tableName].push(value)

                return;
            }

            this.remove(value, tableName)
        },

        remove(value, tableName) {
            const index = this.selected[tableName].indexOf(value);
            if (index > -1) {
                this.selected[tableName].splice(index, 1);
            }
        },

        get(tableName) {
            return this.selected[tableName] ?? ''
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

    window.pgBulkActions   = window.Alpine.store('pgBulkActions')
})
