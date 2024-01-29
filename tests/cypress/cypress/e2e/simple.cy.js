describe('simple', () => {
    it('can visit simple page', () => {
        cy.visit('/')
        cy.contains('Simple')
    })
})
