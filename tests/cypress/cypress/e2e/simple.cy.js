describe('simple', () => {
    it('can visit simple page', () => {
        cy.visit('/simple')
        cy.contains('Simple')
    })
})
