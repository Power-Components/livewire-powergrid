window.pgAlpine.data('pgRenderRowTemplate', () => ({
    templateContent: null,
    rendered: null,
    parentId: null,

    init() {
        const raw = this.$el.dataset.pgParams
        const params = raw ? JSON.parse(raw) : {}

        this.templateContent = params?.templateContent ?? null
        this.parentId = params?.parentId ?? null

        if (!this.templateContent || typeof this.templateContent !== 'object') {
            return
        }

        const templateName = Object.keys(this.templateContent)[0]

        if (!templateName) {
            return
        }

        const templates = window[`pgRowTemplates_${this.parentId ?? this.$wire.id}`]
        const template = templates?.[templateName]

        if (!template) {
            return
        }

        const content = this.templateContent[templateName]

        if (!content) {
            return
        }

        let renderedTemplate = template

        for (const [key, value] of Object.entries(content)) {
            const placeholder = `{{ ${key} }}`
            const safeValue = String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
            renderedTemplate = renderedTemplate.replaceAll(placeholder, safeValue)
        }

        this.rendered = renderedTemplate
    },
}))
