describe('Terminal Document Test', () => {
	it('Visit Local DocTrack', () => {
		cy.visit('127.0.0.1:8000')
		cy.get('input[name=username]').type('go')
		cy.get('input[name=password]').type('secret')
        cy.get('button[type=submit]').click()
        cy.contains('Search').siblings('input').type('New Documents')
        cy.get(':nth-child(1) > :nth-child(10) > .v-icon').click()
        cy.get('.row > :nth-child(2) > .v-btn').click()
        cy.contains('Approved by').parent().type('Atty Paul Jazmin')
        cy.contains('Remarks').parent().type('Baka Kano to!')
        cy.get('.my-2 > .v-btn > .v-btn__content').click()
        cy.get('.v-card__actions > :nth-child(3)').click()
	})
  })