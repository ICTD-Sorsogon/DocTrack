describe('My First Test', () => {
	it('Visit Local DocTrack', () => {
		cy.visit('127.0.0.1:8000')
		cy.get('input[name=username]').type('admin')
		cy.get('input[name=password]').type('secret')
		cy.get('button[type=submit]').click()
		cy.get('.v-card__title > .row > .v-btn').click()
		cy.contains('Document Title/Subject').siblings('input').type('New Documents')
		cy.contains('Document Type').parent().click()
		cy.contains('Salary').parent().click()
		cy.contains('Destination Office').parent().click()
		cy.contains('Office of the Vice Governor').parent().click()
		cy.contains('Sender Name').parent().type('Cypress test')
		cy.contains('Page Count').parent().type(20)
		cy.contains('Attachment').parent().type(10)
		cy.contains('Remarks').parent().type('This is cypres yo!')
		cy.contains('Save').parent().click()
	})
  })