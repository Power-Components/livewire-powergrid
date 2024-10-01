export default (params) => ({
    rowId: params?.rowId ?? null,
    parentId: params?.parentId ?? null,
    toHtml() {
        let actions = null;
        const wireId = this.parentId ?? this.$wire.id;

        if (this.rowId) {
            const pgActions = window[`pgActions_${wireId}`];

            if (pgActions && pgActions[this.rowId] !== undefined) {
                actions = pgActions[this.rowId];
            }
        } else {
            actions = window[`pgActionsHeader_${wireId}`];
        }

        if (typeof actions !== "object" || actions === null) {
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
                    let attributesStr = this.buildAttributesString(action);

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

        return html;
    },

    shouldHideAction(action) {
        if (action.can === false) {
            return true;
        }

        let hideAction = false;
        if (action.rules && Object.values(action.rules).length > 0) {
            Object.values(action.rules).forEach((ruleObj) => {
                if (
                    ruleObj.action.includes(action.action)
                    && ruleObj.apply
                    && ruleObj.rule.hide) {
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
                if (
                    ruleObj.apply
                    && ruleObj.action.includes(action.action)
                    && ruleObj.replaceHtml) {
                    replaceHtml = ruleObj.replaceHtml;
                }
            });
        }
        return replaceHtml;
    },

     buildAttributesString(action) {
        let attributes = action.attributes ?? []

         if (action.rules && Object.values(action.rules).length > 0) {
             Object.values(action.rules).forEach((ruleObj) => {
                 if (
                     ruleObj.apply
                     && ruleObj.action.includes(action.action)
                 ) {
                     if (ruleObj.rule.setAttribute && ruleObj.rule.setAttribute.length > 0) {
                         Object.values(ruleObj.rule.setAttribute).forEach((attribute) => {
                             attributes[attribute.attribute] = attribute.value
                         })
                     }
                 }
             });
         }

        return Object.entries(attributes)
            .map(([key, value]) => ` ${key}="${value}"`)
            .join('');
    },

     processIcon(action) {
        const iconAttributesStr = Object.entries(action.iconAttributes ?? [])
            .map(([key, value]) => ` ${key}="${value}"`)
            .join('');

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
});
