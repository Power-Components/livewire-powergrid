describe('Action Rules::checkbox', () => {
    beforeEach(() => {
        cy.visit('/examples/cypress?ruleType=checkbox');
    });

    it.skip('can visit page', () => {
        cy.contains('Cypress')
    })

    it.skip('should be able to add class attribute with setAttribute when dishId == 1', () => {
        let $rules = '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 1)\n' +
            '                ->setAttribute(\'class\', \'!text-red-500\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0).find('div label input')
            .should('have.class', '!text-red-500');

        cy.get('.power-grid-table tbody tr').eq(1).find('div label input')
            .should('not.have.class', '!text-red-500');

        cy.get('.power-grid-table tbody tr').eq(2).find('div label input')
            .should('not.have.class', '!text-red-500');
    })

    it.skip('should be able to add multiple class conditions with setAttribute', () => {
        let $rules = '' +
            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 1)\n' +
            '                ->setAttribute(\'class\', \'apply-css-class\'),' +

            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 1)\n' +
            '                ->setAttribute(\'class\', \'apply-another-css-class\'),' +

            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 2)\n' +
            '                ->setAttribute(\'id\', \'apply-id\')';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0).find('div label input')
            .should('have.class', 'apply-css-class')
            .should('have.class', 'apply-another-css-class')
            .should('not.have.id', 'apply-id');

        cy.get('.power-grid-table tbody tr').eq(1).find('div label input')
            .should('not.have.class', 'apply-css-class')
            .should('not.have.class', 'apply-another-css-class')
            .should('have.id', 'apply-id');

        cy.get('.power-grid-table tbody tr').eq(2).find('div label input')
            .should('not.have.class', 'apply-css-class')
            .should('not.have.class', 'apply-another-css-class')
            .should('not.have.id', 'apply-id');
    })

    it.skip('should be able to add disabled attribute when dishId === 1 and 3', () => {
        let $rules = '' +
            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 1)\n' +
            '                ->disable(),' +

            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 3)\n' +
            '                ->disable()';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0).find('div label input')
            .should('have.attr', 'disabled');

        cy.get('.power-grid-table tbody tr').eq(1).find('div label input')
            .should('not.have.attr', 'disabled');

        cy.get('.power-grid-table tbody tr').eq(2).find('div label input')
            .should('have.attr', 'disabled');
    })

    it.skip('should be able to add multiple class conditions using Rule::checkbox -> setAttribute', () => {
        let $rules = '' +
            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 1)\n' +
            '                ->hide(),' +

            '\\PowerComponents\\LivewirePowerGrid\\Facades\\Rule::checkbox()\n' +
            '                ->when(fn ($row) => $row->id == 3)\n' +
            '                ->hide()';

        cy.get('[data-cy=dynamic-rules]').type($rules)

        cy.get('[data-cy=apply-rules]').click()

        cy.get('.power-grid-table tbody tr').eq(0).find('div label')
            .should('have.length', 0);

        cy.get('.power-grid-table tbody tr').eq(1).find('div label')
            .should('have.length', 1);

        cy.get('.power-grid-table tbody tr').eq(0).find('div label')
            .should('have.length', 0);
    })
})
