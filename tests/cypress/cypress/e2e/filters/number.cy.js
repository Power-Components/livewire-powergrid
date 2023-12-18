describe('simple', () => {
    beforeEach(() => {
        cy.visit('/cypress?testType=filters');
    });

    it('test', () => {
        const expectedFilter = '{"number":{"id":{"start":"2","end":"4"}}}'

        cy.get('[wire\\:model="filters.number.id.start"]')
            .type('2').should('have.value', '2');

        cy.get('[wire\\:model="filters.number.id.end"]')
            .type('4').should('have.value', '4');

        cy.get('[data-cy="filters-log"]').should('contain.text', expectedFilter);

        cy.get('.power-grid-table tbody tr').then(($el) => {
            $el.each((index, row) => {

                const firstTdText = Cypress.$(row).find('td').eq(0).text();
                expect(firstTdText).not.to.include('1');
                expect(firstTdText).not.to.include('5');
                expect(firstTdText).not.to.include('6');
            });
        })
    });
})
