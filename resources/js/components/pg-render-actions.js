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
        this.storageKey = `pg_session_${this.$wire.tableName}_row_${this.rowId}`;
        this.cookieKey = `pg_cookie_${this.$wire.tableName}_row_${this.rowId}`;
    },

    toHtml() {
        if (localStorage.getItem(this.storageKey)) {
            return localStorage.getItem(this.storageKey);
        }

        let actions
        if (this.rowId) {
            actions = window[`pgActions_${this.parentId ?? this.$wire.id}`][this.rowId];
        } else {
            actions = window[`pgActionsHeader_${this.$wire.id}`];
        }

        if (typeof actions !== "object") {
            return '';
        }

        let html = ''

        actions.forEach((action) => {
            let hideAction = false;
            let replaceHtml = null;

            if (action.rules && Object.values(action.rules).length > 0) {
                Object.values(action.rules).forEach((ruleObj) => {

                    if (ruleObj.apply) {
                        if (ruleObj.rule.hide) {
                            hideAction = true;
                        } else if (ruleObj.rule.setAttribute) {
                            ruleObj.rule.setAttribute.forEach(attrRule => {
                                if (action.attributes[attrRule.attribute]) {
                                    action.attributes[attrRule.attribute] += ` ${attrRule.value}`;
                                } else {
                                    action.attributes[attrRule.attribute] = attrRule.value;
                                }
                            });
                        }
                        if (ruleObj.replaceHtml) {
                            replaceHtml = ruleObj.replaceHtml;
                        }
                    }
                });
            }

            if (!hideAction) {
                if (replaceHtml) {
                    html += replaceHtml;
                } else {
                    let attributesStr = '';

                    Object.entries(action.attributes).forEach(([key, value]) => {
                        attributesStr += ` ${key}="${value}"`;
                    });

                    if (action.icon) {
                        const iconAttributesStr = Object.entries(action.iconAttributes)
                            .filter(([key, value]) => value != null)
                            .map(([key, value]) => `${key}="${value}"`)
                            .join(' ');

                        let iconHtml = window.pgResourceIcons[action.icon];

                        if (typeof iconHtml === "undefined") {
                            console.warn(`PowerGrid: Unable to load icons in javascript window in row: [${this.rowId}]`)
                            return;
                        }

                        iconHtml = iconHtml.replace(/<([^\s>]+)([^>]*)>/, (match, tagName, existingAttributes) => {
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

                        if (action.slot) {
                            html += `<${action.tag ?? 'button'} ${attributesStr}>${iconHtml} ${action.slot}</${action.tag ?? 'button'}>`;
                        } else {
                            html += `<${action.tag ?? 'button'} ${attributesStr}>${iconHtml}</${action.tag ?? 'button'}>`;
                        }
                    } else {
                        html += `<${action.tag ?? 'button'} ${attributesStr}>${action.slot}</${action.tag ?? 'button'}>`;
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
