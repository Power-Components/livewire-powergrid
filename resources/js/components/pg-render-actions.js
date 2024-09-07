export default (params) => ({
    rowId: params?.rowId ?? null,
    storageKey: null,
    cookieKey: null,
    parentId: params?.parentId ?? null,
    init() {
        this.setKeys();

        window.addEventListener('beforeunload', () => {
            localStorage.removeItem(this.storageKey);
            document.cookie = this.cookieKey + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });
    },

    setKeys() {
        if (this.rowId) {
            this.storageKey = `pg_session_${this.$wire.tableName}_row_${this.rowId}`;
            this.cookieKey = `pg_cookie_${this.$wire.tableName}_row_${this.rowId}`;

            return;
        }

        this.storageKey = `pg_session_${this.$wire.tableName}_header_actions`;
        this.cookieKey = `pg_cookie_${this.$wire.tableName}_header_actions`;
    },

    toHtml() {
        const value = localStorage.getItem(this.storageKey)

        if (typeof value === 'string' && value.length > 0) {
            return value;
        }

        const actions = this.rowId
            ? window[`pgActions_${this.parentId ?? this.$wire.id}`][this.rowId]
            : window[`pgActionsHeader_${this.$wire.id}`];

        if (typeof actions !== "object") {
            return '';
        }

        let html = ''

        actions.forEach((action) => {
            let hideAction = this.shouldHideAction(action);
            let replaceHtml = this.getReplaceHtml(action);

            if (!hideAction) {
                if (replaceHtml) {
                    html += replaceHtml;
                } else {
                    let attributesStr = this.buildAttributesString(action.attributes);

                    if (action.icon) {
                        let iconHtml = this.processIcon(action);
                        if (!iconHtml) return;

                        html += this.buildActionHtmlWithIcon(action, attributesStr, iconHtml);
                    } else {
                        html += this.buildActionHtml(action, attributesStr);
                    }
                }
            }
        });

        if (this.checkLocalStorageFreeSpace()) {
            localStorage.setItem(this.storageKey, html);

            document.cookie = this.cookieKey + '=true; path=/';
        }

        return html;
    },

    shouldHideAction(action) {

        if (action.can === false) {
            return true;
        }

        let hideAction = false;
        if (action.rules && Object.values(action.rules).length > 0) {
            Object.values(action.rules).forEach((ruleObj) => {
                if (ruleObj.apply && ruleObj.rule.hide) {
                    hideAction = true;
                }
            });
        }
        return hideAction;
    },

     getReplaceHtml(action) {
        let replaceHtml = null;
        if (action.rules && Object.values(action.rules).length > 0) {
            Object.values(action.rules).forEach((ruleObj) => {
                if (ruleObj.apply && ruleObj.replaceHtml) {
                    replaceHtml = ruleObj.replaceHtml;
                }
            });
        }
        return replaceHtml;
    },

     buildAttributesString(attributes) {
        return Object.entries(attributes)
            .map(([key, value]) => ` ${key}="${value}"`)
            .join('');
    },

     processIcon(action) {
        const iconAttributesStr = this.buildAttributesString(action.iconAttributes);
        let iconHtml = window.pgResourceIcons[action.icon];

        if (typeof iconHtml === "undefined") {
            console.warn(`PowerGrid: Unable to load icons in javascript window in row: [${this.rowId}]`);
            return null;
        }

        return this.replaceIconAttributes(iconHtml, iconAttributesStr);
    },

    replaceIconAttributes(iconHtml, iconAttributesStr) {
        return iconHtml.replace(/<([^\s>]+)([^>]*)>/, (match, tagName, existingAttributes) => {
            let updatedAttributes = existingAttributes.trim();
            const newAttributes = iconAttributesStr.replace(/class="([^"]*)"/, (classMatch, newClass) => {
                if (updatedAttributes.includes('class=')) {
                    updatedAttributes = updatedAttributes.replace(/class="([^"]*)"/, (classMatch, existingClass) =>
                        `class="${existingClass} ${newClass.trim()}"`
                    );
                } else {
                    updatedAttributes += ` ${classMatch}`;
                }
                return '';
            });
            return `<${tagName} ${updatedAttributes} ${newAttributes}>`;
        });
    },

    buildActionHtmlWithIcon(action, attributesStr, iconHtml) {
        if (action.slot) {
            return `<${action.tag ?? 'button'} ${attributesStr}>${iconHtml} ${action.slot}</${action.tag ?? 'button'}>`;
        }

        return `<${action.tag ?? 'button'} ${attributesStr}>${iconHtml}</${action.tag ?? 'button'}>`;
    },

    buildActionHtml(action, attributesStr) {
        return `<${action.tag ?? 'button'} ${attributesStr}>${action.slot}</${action.tag ?? 'button'}>`;
    },

    checkLocalStorageFreeSpace() {
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
