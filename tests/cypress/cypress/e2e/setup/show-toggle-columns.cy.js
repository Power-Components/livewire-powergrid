describe('Toggle columns functionality', () => {
    [
        '/setup-toggle-columns?powerGridTheme=tailwind',
        '/setup-toggle-columns?powerGridTheme=bootstrap'
    ].forEach((route) => {
        beforeEach(() => {
            cy.visit(route);
        });

        const toggleButton = '[data-cy="toggle-columns-setup-toggle-columns"]';
        const toggleFields = {
            id: '[data-cy="toggle-field-id"]',
            name: '[data-cy="toggle-field-name"]',
            email: '[data-cy="toggle-field-email"]',
            created: '[data-cy="toggle-field-created_at_formatted"]',
            actions: '[data-cy="toggle-field-actions"]'
        };

        const tableColumns = {
            id: {
                header: 'th[data-column="id"]',
                cells: 'td[data-column="id"]'
            },
            name: {
                header: 'th[data-column="name"]',
                cells: 'td[data-column="name"]'
            },
            email: {
                header: 'th[data-column="email"]',
                cells: 'td[data-column="email"]'
            },
            created: {
                header: 'th[data-column="created_at_formatted"]',
                cells: 'td[data-column="created_at_formatted"]'
            },
            actions: {
                header: 'th[data-column="actions"]',
                cells: 'td[data-column="actions"]'
            }
        };

        Object.entries(toggleFields).forEach(([field, toggleSelector]) => {
            it(`should toggle the visibility of the ${field} column`, () => {
                cy.get(toggleButton).click();

                cy.get(toggleSelector).click();

                cy.get(tableColumns[field].header).should('have.css', 'display', 'none');

                cy.get(tableColumns[field].cells).each(($cell) => {
                    cy.wrap($cell).should('have.css', 'display', 'none');
                });

                cy.get(toggleSelector).click({force: true});

                cy.get(tableColumns[field].header).should('not.have.css', 'display', 'none');

                cy.get(tableColumns[field].cells).each(($cell) => {
                    cy.wrap($cell).should('not.have.css', 'display', 'none');
                });
            });
        });
    })
});
