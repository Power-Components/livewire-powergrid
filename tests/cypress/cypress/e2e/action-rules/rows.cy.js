describe('Action Rules::rows', () => {
    beforeEach(() => {
        cy.visit('/examples/cypress?ruleType=rows');
    });

    it.skip('can visit page', () => {
        cy.contains('Cypress')
    })

    it.skip('should be able to add class attribute using Rule::rows -> setAttribute on all rows', () => {
        let $rules = '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->setAttribute(\'class\', \'!cursor-pointer\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0)
            .should('have.class', '!cursor-pointer');

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('have.class', '!cursor-pointer');

        cy.get('.power-grid-table tbody tr').eq(2)
            .should('have.class', '!cursor-pointer');

        cy.get('.power-grid-table tbody tr').eq(3)
            .should('have.class', '!cursor-pointer');
    })

    it.skip('should be able to add class attribute using Rule::rows -> setAttribute when dishId == 1', () => {
        let $rules = '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id == 1)' +
            '->setAttribute(\'class\', \'!cursor-pointer\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0)
            .should('have.class', '!cursor-pointer');

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('not.have.class', '!cursor-pointer');
    })

    it.skip('should be able to add class attribute using Rule::rows -> setAttribute when dishId != 1', () => {
        let $rules = '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id != 1)' +
            '->setAttribute(\'class\', \'!cursor-pointer\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0)
            .should('not.have.class', '!cursor-pointer');

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('have.class', '!cursor-pointer');
    })

    it.skip('should be able to add multiple class conditions using Rule::rows -> setAttribute', () => {
        let $rules = '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id == 1)' +
            '->setAttribute(\'class\', \'!bg-red-100\'), ' +
            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id == 2)' +
            '->setAttribute(\'class\', \'!bg-blue-100\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0)
            .should('have.class', '!bg-red-100');

        cy.get('.power-grid-table tbody tr').eq(0)
            .should('not.have.class', '!bg-green-100');

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('not.have.class', '!bg-green-100');

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('not.have.class', '!bg-red-100');
    })

    it.skip('should be able to add multiple attributes using Rule::rows -> setAttribute when dishId == 3', () => {
        let $rules = '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id == 2)' +
            '->setAttribute(\'class\', \'!bg-red-100\'), ' +
            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id == 2)' +
            '->setAttribute(\'id\', \'custom-id\'),' +
            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::rows()' +
            '->when(fn($row) => $row->id == 2)' +
            '->setAttribute(\'title\', \'Custom title !\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('have.class', '!bg-red-100')
            .should('have.id', 'custom-id')
            .should('have.attr', "title")
            .then(title => expect(title).to.match(/Custom title !/));

        cy.get('.power-grid-table tbody tr').eq(2)
            .should('not.have.class', '!bg-red-100')
            .should('not.have.id', 'custom-id')
            .should('not.have.attr', "title");
    })
})
