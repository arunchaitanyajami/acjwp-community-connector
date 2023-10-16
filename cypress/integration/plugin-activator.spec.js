describe('Test that the network is installed plugin', () => {
    beforeEach(() => {
        cy.visitAdmin();
    })

    it('can visit plugin', () => {
        cy.visit(`/wp-admin/plugins.php`);
    })

    it('can see activated plugins', () => {
        cy.visit(`/wp-admin/plugins.php`);
        cy.get('[data-slug="acjwp-community-connector"]')
            .should('contain.text', 'Deactivate');
    });
});
