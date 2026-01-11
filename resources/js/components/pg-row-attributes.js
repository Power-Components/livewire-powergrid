export default (params) => ({
    rowId: params.rowId,
    rules: params.rules,
    attributes: [],
    theme: [],
    init() {
        if (this.rules) {
            Object.values(this.rules).forEach((rule) => {
                if ((rule.applyLoop || rule.apply) && rule?.attributes) {
                    this.attributes.push(rule.attributes)
                }
            })
        }
    },

    getAttributes() {
        let attributes = {
            class: this.$el.getAttribute('class') || '',
        };

        this.attributes.forEach(attribute => {
            Object.keys(attribute).forEach(key => {
                if (typeof attribute[key] === 'object') {
                    attributes[attribute[key].key] = attribute[key].value;
                } else {
                    if (!attributes[key]) {
                        attributes[key] = attribute[key];
                    } else {
                        attributes[key] += ` ${attribute[key]}`
                    }
                }
            });
        });

        return attributes;
    }
});
