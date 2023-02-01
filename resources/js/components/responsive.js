document.addEventListener('alpine:init', () => {
    window.Alpine.directive('responsive', (el, {expression, modifiers}, {Alpine, cleanup}) => {

        let activeColumn

        const breakPoints = {
            'xs': 576,
            'sm': 640,
            'md': 768,
            'lg': 1024,
            'xl': 1280,
            '2xl': 1536,
        }

        window.addEventListener('addFlexToActions', () => {
            const elements = document.querySelectorAll('.pg-action');

            elements.forEach((element) => {
                element.classList.add('flex')
            });
        })

        const resizeObserver = new ResizeObserver(entries => {
            for (const entry of entries) {
                if (entry.contentRect.width > 0) {
                    handleClasses(entry.contentRect.width);
                }
            }

            if (activeColumn) {
                window.pgResponsiveColumns.add(activeColumn, el.getAttribute('column-title'), 'default', el.getAttribute('component'))
            }
        });

        Alpine.nextTick(() => resizeObserver.observe(getTarget()))

        cleanup(() => {
            resizeObserver.disconnect();
        })

        function handleClasses(width)
        {
            const breakPoint = getClosestBreakPoint(width);

            if (!breakPoint && expression) {
                return setClasses(expression, el.getAttribute('column-field'));
            }

            if (breakPoint) {
                setClasses(el.getAttribute(breakPoint), el.getAttribute('column-field'));
            } else {
                removeOldClasses(el.getAttribute('column-field'));
            }
        }

        function getClosestBreakPoint(width)
        {
            let closestBreakPoint = null;

            const element = el.attributes;

            [...element].forEach(({ name }) => {
                if (name.includes('responsive-') && !name.includes('responsive-default')) {
                    const breakPoints = name.replace('responsive-', '').split('-')
                    const min = breakPoints[0]

                    if (width >= min) {
                        closestBreakPoint = el.getAttribute(name);
                    }
                }
            })

            for (const breakPoint of Object.keys(breakPoints)) {
                if (width >= breakPoints[breakPoint] && el.getAttribute(breakPoint)) {
                    closestBreakPoint = breakPoint;
                }
            }

            return closestBreakPoint;
        }

        function removeOldClasses()
        {
            const classes = el.getAttribute('x-responsive-classes');

            if (classes != null) {
                el.classList.remove(...classes.split(' '));

                activeColumn = null
            }
        }

        function setClasses(classes, column) {
            removeOldClasses(column);

            el.setAttribute('x-responsive-classes', classes);

            if (classes != null) {
                el.classList.add(...classes.split(' '));
                activeColumn = column
            }
        }

        function getTarget()
        {
            const target = modifierValue(modifiers, 'target', null) ?? el.getAttribute('target');

            return document.querySelector([`[x-ref="${target}"]`]) ?? el;
        }

        function modifierValue(modifiers, value, notFoundValue) {
            const index = modifiers.indexOf(value);

            if (index === -1) return notFoundValue;

            const modifierValue = index + 1;

            return modifiers[modifierValue] ?? notFoundValue;
        }
    })
})
