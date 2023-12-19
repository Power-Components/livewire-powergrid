describe('simple', () => {
    beforeEach(() => {
        cy.visit('/cypress?testType=filters');
    });

    it('test', () => {
        const expectedFilter = '{"input_text":{"name":"d","email":"d"}}' // Daniel, Claudio

        cy.get('[wire\\:model="filters.input_text.name"]')
            .type('d').should('have.value', 'd');

        cy.get('[wire\\:model="filters.input_text.email"]')
            .type('d').should('have.value', 'd');

        cy.get('[data-cy="filters-log"]').should('contain.text', expectedFilter);

        cy.get('.power-grid-table tbody tr').then(($el) => {
            $el.each((index, row) => {

                const name = Cypress.$(row).find('td').eq(1).text();

                expect(name).not.to.include('Luan');
                expect(name).not.to.include('Tio Jobs');
            });
        })
    });
})
