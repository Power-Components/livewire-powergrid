document.addEventListener('alpine:init', () => {
    window.Alpine.store('editOnClickValidation', {
        pending: [],

        set(value, textContent) {
            this.pending = this.pending.filter(item => item.value !== value);
            this.pending.push({ value, textContent });
        },

        has(value) {
            return this.pending.some(item => item.value === value);
        },

        get(value) {
            return this.pending.find(item => item.value === value);
        },

        notContains(value) {
            return this.pending.length > 0 && !this.has(value);
        },

        clear() {
            this.pending = [];
        },

        getTextContent(value) {
            const item = this.pending.find(item => item.value === value);
            return item ? item.textContent : null;
        }
    });

    window.editOnClickValidation = window.Alpine.store('editOnClickValidation')
})
