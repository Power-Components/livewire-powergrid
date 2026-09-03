window.pgAlpine.data('pgFilterPanel', () => ({
    open: false,

    init() {
        this.ensureDraftObject()

        if (this.$el.dataset.openOnLoad === 'true') {
            this.open = true
        }
    },

    ensureDraftObject() {
        const raw = this.$wire.draftFilters

        if (raw === null || raw === undefined || Array.isArray(raw)) {
            this.$wire.draftFilters = {}
        }
    },

    async toggle() {
        if (!this.$wire.filterPanelLoaded) {
            await this.$wire.loadFilterPanel()
            this.ensureDraftObject()
            this.open = true

            return
        }

        this.open = !this.open
    },

    badgeCount() {
        const filters = this.$wire.enabledFilters || []
        const unique = new Set()

        filters.forEach((filter) => {
            if ((filter.source || null) === 'filterBuilder') {
                return
            }

            let field = String(filter.field || '')
            field = field.replace(/_start$/, '').replace(/_end$/, '')

            if (field !== '') {
                unique.add(field)
            }
        })

        return unique.size
    },

    closeOnOutside(event) {
        const target = event.target

        if (!(target instanceof Element)) {
            this.open = false

            return
        }

        if (target.closest('.flatpickr-calendar, .ts-dropdown, .ss-content')) {
            return
        }

        this.open = false
    },

    apply() {
        this.open = false
        this.ensureDraftObject()

        const draft = this.draftFromDom()

        this.$wire.draftFilters = draft
        this.$wire.applyFilters(draft)
    },

    clearAll() {
        this.open = false
        this.$wire.clearAllFilters()
    },

    draftFromDom() {
        let draft = {}

        try {
            const raw = this.$wire.draftFilters

            // draftFilters seeds as a PHP array (serialized to a JS []); string
            // keys set on an array are dropped by JSON.stringify, so coerce to a
            // plain object before nesting field values into it.
            draft = raw && !Array.isArray(raw) ? JSON.parse(JSON.stringify(raw)) : {}
        } catch (e) {
            draft = {}
        }

        this.$el.querySelectorAll('input, select, textarea').forEach((el) => {
            const path = draftPath(el)

            if (!path) {
                return
            }

            setNested(draft, path, inputValue(el))
        })

        return draft
    },
}))

function draftPath(el) {
    const marked = el.getAttribute('data-pg-draft')

    if (marked) {
        return marked
    }

    const property = wireModelProperty(el)

    if (!property || property.indexOf('draftFilters') !== 0) {
        return null
    }

    const path = property.replace(/^draftFilters\.?/, '')

    return path === '' ? null : path
}

function wireModelProperty(el) {
    for (const attr of el.attributes) {
        if (attr.name === 'wire:model' || attr.name.startsWith('wire:model.')) {
            return attr.value
        }
    }

    return null
}

function setNested(obj, path, value) {
    const parts = path.split('.')
    let current = obj

    for (let i = 0; i < parts.length - 1; i++) {
        const key = parts[i]

        if (typeof current[key] !== 'object' || current[key] === null || Array.isArray(current[key])) {
            current[key] = {}
        }

        current = current[key]
    }

    current[parts[parts.length - 1]] = value
}

function inputValue(el) {
    if (el.type === 'checkbox') {
        return el.checked
    }

    if (el.multiple) {
        return Array.from(el.selectedOptions).map((option) => option.value)
    }

    return el.value
}
