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

    const itemsSorted = [].slice.call(items).sort(function (a,b) {
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
        element.querySelectorAll(`tbody:not(tbody[expand]) tr td:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
        element.querySelectorAll(`thead tr th:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
    }
}

function fillTableExpand(element, hideItems) {
    if (!element.querySelectorAll('table tbody[expand] tr td div').length) return

    for (const expands of element.querySelectorAll('table tbody[expand] tr td div')) {
        expands.innerHTML = ""
    }

    if (!hideItems.length) return

    for (const hideItem of hideItems) {
        const rows = element.querySelectorAll('table tbody:not(tbody[expand])')

        for (const row of rows) {
            const expandContainer = row.nextElementSibling?.querySelector('tr td div')

            if (!expandContainer) continue

            let rowName = element.querySelector(`table thead tr th:nth-child(${hideItem}) span[data-value]`).textContent

            const rowValue = row.querySelector(`tr:last-child td:nth-child(${hideItem})`)?.innerHTML

            if (rowName.length) {
                rowName += ':'
            }
            if (!expandContainer.querySelector(`div[data-expand-item-${hideItem}]`)) {
                expandContainer.innerHTML += `<div class="responsive-row-expand-item-container" data-expand-item-${hideItem}>
                    <span class="font-bold responsive-row-expand-item-name">${rowName}</span>
                    <span class="responsive-row-expand-item-value">${rowValue}</span>
                </div>`
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
