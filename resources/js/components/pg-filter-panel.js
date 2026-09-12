window.pgAlpine.data('pgFilterPanel', () => ({
    open: false,

    init() {
        this.ensureDraftObject()

        if (this.$el.dataset.openOnLoad === 'true') {
            this.open = true
        }

        // Smart positioning: keep the panel anchored to the trigger but always
        // inside the viewport (clamp + flip), so it never gets clipped off-screen.
        this.$watch('open', (open) => {
            if (open) {
                this.ensureDraftObject()
                this.$nextTick(() => this.positionPanel())
            }
        })

        this.reposition = () => {
            if (this.open) {
                this.positionPanel()
            }
        }

        window.addEventListener('resize', this.reposition)
        window.addEventListener('scroll', this.reposition, true)
    },

    destroy() {
        window.removeEventListener('resize', this.reposition)
        window.removeEventListener('scroll', this.reposition, true)
    },

    positionPanel() {
        const trigger = this.$el.querySelector('[data-cy="filter-dropdown-trigger"]')
        const panel = this.$el.querySelector('[data-cy="filter-dropdown-panel"]')

        if (!trigger || !panel) {
            return
        }

        // Below the breakpoint the panel is a full-width sheet handled by CSS.
        if (window.innerWidth < 768) {
            panel.style.position = panel.style.top = panel.style.left = ''
            panel.style.right = panel.style.width = panel.style.maxHeight = ''

            return
        }

        const margin = 8
        const gap = 8
        const t = trigger.getBoundingClientRect()
        const width = Math.min(672, window.innerWidth - margin * 2) // 672px = 42rem

        // Right-align to the trigger, then clamp inside the viewport.
        let left = t.right - width
        left = Math.max(margin, Math.min(left, window.innerWidth - width - margin))

        // Prefer below the trigger; flip above when there is not enough room.
        const panelHeight = panel.offsetHeight || 0
        const below = window.innerHeight - t.bottom - gap - margin
        const above = t.top - gap - margin
        const flip = below < panelHeight && above > below

        const top = flip ? Math.max(margin, t.top - gap - panelHeight) : t.bottom + gap
        const maxHeight = flip ? above : below

        panel.style.position = 'fixed'
        panel.style.width = `${width}px`
        panel.style.left = `${left}px`
        panel.style.right = 'auto'
        panel.style.top = `${top}px`
        panel.style.bottom = 'auto'
        panel.style.maxHeight = `${Math.max(160, maxHeight)}px`
    },

    ensureDraftObject() {
        ensureDraftObject(this.$wire)
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
        const draft = collectDraftFrom(this.$root)
        this.open = false
        this.$wire.applyFilters(draft)
    },

    clearAll() {
        this.$wire.clearAllFilters()
    },
}))

window.pgAlpine.data('pgFilterFlyout', () => ({
    init() {
        ensureDraftObject(this.$wire)
    },

    apply() {
        const draft = collectDraftFrom(this.$root)
        this.$wire.showFilters = false
        this.$wire.applyFilters(draft)
    },
}))

function ensureDraftObject(wire) {
    const raw = wire.draftFilters

    if (raw === null || raw === undefined || Array.isArray(raw)) {
        wire.draftFilters = {}
    }
}

function collectDraftFrom(root) {
    const draft = {}

    root.querySelectorAll('input, select, textarea').forEach((el) => {
        if (['button', 'submit', 'reset', 'file', 'image'].includes(el.type)) {
            return
        }

        const path = draftPath(el)

        if (!path) {
            return
        }

        setNested(draft, path, inputValue(el))
    })

    return draft
}

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
        if (!isModelAttribute(attr.name)) {
            continue
        }

        const value = attr.value

        if (value && value.indexOf('draftFilters') === 0) {
            return value
        }
    }

    return null
}

function isModelAttribute(name) {
    return name === 'wire:model'
        || name.startsWith('wire:model.')
        || name === 'x-model'
        || name.startsWith('x-model.')
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
