export default class {
	constructor ( element ) {
		this.element = element;
	}

	setup () {

		// Setup confirmation box.
		if ( this.element.dataset.confirm ) {
			const form = this.element.closest( 'form' );
			const confirmation = this.element.querySelector( '.js-confirmation' );
			const confirmationButtons = confirmation.querySelectorAll( '.js-confirmation-button' );

			// Prevent form submit to open the confirmation box.
			form.addEventListener( 'submit', e => {
				e.preventDefault();
			});

			this.element.addEventListener( 'click', ({ target }) => {

				// Bail early if the confirmation box itself was clicked.
				if (
					target.classList.contains( 'js-confirmation' ) ||
					target.closest( '.js-confirmation' )
				) return;

				// Open the confirmation box if the button was clicked.
				if ( ! this.activeConfirmation ) {
					confirmation.classList.add( 'active' );
					this.activeConfirmation = confirmation;

				// Hide the confirmation box if it was visible.
				} else {
					confirmation.classList.remove( 'active' );
					this.activeConfirmation = null;
				}
			});

			// Submit the form, or not, depending on the confirmation button clicked.
			confirmationButtons.forEach( button => {
				button.addEventListener( 'click', () => {
					if ( button.hasAttribute( 'data-close' ) ) {
						confirmation.classList.remove( 'active' );
						this.activeConfirmation = null;
					} else {
						form.action = form.dataset.action;
						form.submit();
					}
				});
			});
		}
	}
}
