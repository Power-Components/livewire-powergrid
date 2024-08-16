export default (params) => ({
    rowId: params?.rowId ?? null,
    field: params?.field ?? null,
    templateContent: params?.templateContent ?? null,
    rendered: null,
    parentId: params?.parentId ?? null,
    init() {
        const column = this.$wire.columns.filter((column) => column.field == this.field || column.dataField == this.field)[0];

        if (column?.template.toString() !== '') {
            const templateName = Object.keys(this.templateContent)[0];

            const template = window[`pgRowTemplates_${this.parentId ?? this.$wire.id}`][templateName];

            const content = this.templateContent[templateName];

            if (content) {
                let renderedTemplate = template;

                for (const [key, value] of Object.entries(content)) {
                    const placeholder = `{{ ${key} }}`;
                    const safeValue = String(value).replace(/'/g, "\\'");
                    renderedTemplate = renderedTemplate.replaceAll(placeholder, safeValue);
                }

                this.rendered = renderedTemplate;
            }
        }
    },
});
