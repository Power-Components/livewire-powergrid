describe('Header can (show/hide)', () => {
    [
        '/header-can?powerGridTheme=tailwind',
        '/header-can?powerGridTheme=bootstrap'
    ].forEach((route) => {
        beforeEach(() => {
            cy.visit(route);
        });

        it('can se only visible header button', () => {
            cy.get('[data-cy="btn-header-visible"]').should("be.visible");

            cy.get('body').should('contain.text', 'Visible');

            cy.get('body').should('not.contain.text', 'Invisible');
        })
    })
});
