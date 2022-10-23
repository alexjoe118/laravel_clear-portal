export default class {
	constructor ( element ) {
		this.element = element;
	}

	setup () {

		// Open the modal when clicking the trigger.
		if ( this.element.id ) {
			const triggerId = this.element.id + '-trigger';
			let triggers;

			// Prepare all the triggers, if there are multiple or not.
			if ( this.element.classList.contains( 'multiple-triggers' ) ) {
				triggers = document.querySelectorAll( '.' + triggerId );
			} else {
				triggers = [ document.getElementById( triggerId ) ];
			}

			triggers.forEach( trigger => {
				trigger.addEventListener( 'click', () => {
					this.element.classList.add( 'active' );

					const event = new CustomEvent( 'open', {
						detail: { trigger: trigger }
					});

					this.element.dispatchEvent( event );
				});
			});
		}

		// Close modal when clicking on the background or the close button.
		this.element.addEventListener( 'click', ({ target }) => {
			if (
				target.classList.contains( 'js-modal' ) ||
				target.classList.contains( 'js-modal-close' )
			) {
				this.element.classList.remove( 'active' );
			}
		});
	}
}
