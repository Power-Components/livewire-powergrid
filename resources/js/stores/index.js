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

    window.editablePending = window.Alpine.store('editablePending')
})
