export default (params) => ({
    rowId: params.rowId,
    storageKey: null,
    cookieKey: null,
    init() {
        this.setKeys();

        window.addEventListener('beforeunload', () => {
            localStorage.removeItem(this.storageKey);
            document.cookie = this.cookieKey + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });
    },

    setKeys() {
        this.storageKey = `pg_s_${this.$wire.tableName}_row_${this.rowId}`;
        this.cookieKey = `pg_c_${this.$wire.tableName}_row_${this.rowId}`;
    },

    toHtml() {
        if (localStorage.getItem(this.storageKey)) {
            return localStorage.getItem(this.storageKey);
        }

        const html = this.$wire.actionsHtml[this.rowId];

        if (this.checkSpace()) {
            localStorage.setItem(this.storageKey, html);

            document.cookie = this.cookieKey + '=true; path=/';
        }

        return html;
    },

    checkSpace() {
        const maxLocalStorageSize = 2 * 1024 * 1024; // 2MB
        let totalSize = 0;

        for (let i = 0; i < localStorage.length; i++) {
            let key = localStorage.key(i);
            let value = localStorage.getItem(key);

            if (value !== null) {
                totalSize += new Blob([value]).size;
            }
        }

        return totalSize <= maxLocalStorageSize
    },
});
