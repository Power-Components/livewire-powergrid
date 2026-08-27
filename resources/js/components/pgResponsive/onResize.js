function showAllItems(element) {
    element.querySelectorAll(`tbody tr td`).forEach((el) => { el.classList.remove('hidden') })
    element.querySelectorAll(`thead tr th`).forEach((el) => { el.classList.remove('hidden') })
}

function getTableUtilWidth(element) {
    let fixedSpace = 0;

    const fixedItems = element.querySelectorAll('table thead tr:nth-child(1) th[fixed]');

    const elementWidth = getElementWidth(element);

    fixedItems.forEach((element) => {
        fixedSpace += getElementWidth(element);
    })

    return elementWidth - fixedSpace;
}

function getItemsToHide(element, tableWidth) {
    const items = [].slice.call(element.querySelectorAll('table thead tr:nth-child(1) th'))

    const visibleThs = Array.from(items).filter(th => {
        return getComputedStyle(th).display !== 'none';
    });

    const itemsSorted = [].slice.call(visibleThs).sort(function (a,b) {
        const firstSortOrder = a.getAttribute('sort_order') ??  999
        const secondSortOrder = b.getAttribute('sort_order') ??  999

        return firstSortOrder - secondSortOrder
    });

    let calc = 0;

    let fitsMoreItems = true;

    const itemsToHide = []

    itemsSorted.forEach((item) => {
        const itemWidth = getElementWidth(item);

        if (item.getAttribute('fixed') !== null) {
            return
        }

        if (fitsMoreItems && calc <= tableWidth && (calc + itemWidth <= tableWidth)) {
            calc += itemWidth;
        } else {
            itemsToHide.push(items.indexOf(item) + 1)
            fitsMoreItems = false
        }
    })

    return itemsToHide
}

function hideItems(element, items) {
    for (const item of items) {
        element.querySelectorAll(`tbody tr[data-pg-row-id] td:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
        element.querySelectorAll(`thead tr th:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
        element.querySelectorAll(`tbody tr[data-pg-inline-filters] td:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
    }
}

function findExpandContainer(row) {
    const rowId = row.getAttribute('data-pg-row-id')
    const root = row.closest('table') || row.parentElement

    if (rowId !== null && root) {
        const escaped = (typeof CSS !== 'undefined' && CSS.escape) ? CSS.escape(rowId) : rowId.replace(/"/g, '\\"')
        const expand = root.querySelector(`tr[expand][data-expand-for="${escaped}"]`)

        if (expand) {
            return expand.querySelector('td div')
        }
    }

    let sibling = row.nextElementSibling

    while (sibling) {
        if (sibling.hasAttribute('expand')) {
            return sibling.querySelector('td div')
        }

        sibling = sibling.nextElementSibling
    }

    return null
}

function fillTableExpand(element, hideItems) {
    const expandDivs = element.querySelectorAll('table tr[expand] td div')

    if (!expandDivs.length) return

    for (const expands of expandDivs) {
        expands.innerHTML = ""
    }

    if (!hideItems.length) return

    const rows = element.querySelectorAll('table tbody tr[data-pg-row-id]')

    for (const hideItem of hideItems) {
        let rowName = element.querySelector(`table thead tr th:nth-child(${hideItem}) span[data-value]`)?.textContent ?? ''

        if (rowName.length) {
            rowName += ':'
        }

        for (const row of rows) {
            const expandContainer = findExpandContainer(row)

            if (!expandContainer) continue

            const sourceCell = row.querySelector(`td:nth-child(${hideItem})`)

            if (!expandContainer.querySelector(`div[data-expand-item-${hideItem}]`)) {
                const item = document.createElement('div')
                item.className = 'responsive-row-expand-item-container'
                item.setAttribute(`data-expand-item-${hideItem}`, '')

                const name = document.createElement('span')
                name.className = 'font-bold responsive-row-expand-item-name'
                name.textContent = rowName

                const value = document.createElement('span')
                value.className = 'responsive-row-expand-item-value'
                if (sourceCell) {
                    value.appendChild(sourceCell.cloneNode(true))
                }

                item.append(name, value)
                expandContainer.appendChild(item)
            }
        }
    }
}

function getElementWidth(element) {
    return parseFloat(element.getBoundingClientRect().width.toFixed(2))
}

export default function (element) {
    showAllItems(element)

    const tableUtilWidth = getTableUtilWidth(element)

    const itemsToHide = getItemsToHide(element, tableUtilWidth)

    fillTableExpand(element, itemsToHide)

    hideItems(element, itemsToHide)
};
