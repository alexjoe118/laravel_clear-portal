import { maskInputs, unmaskInputs } from '~/helpers/mask-inputs';
import { setupComponents } from '~/domain/components';

export default class {
	constructor ( element ) {
		this.element = element;
		this.form = this.element.closest( '.js-form' );
		this.ownership = this.element.querySelector( '.js-ownership' );
		this.partners = [ ...this.element.querySelectorAll( '.js-partner' ) ];
		this.addButton = this.element.querySelector( '.js-add-partner' );
	}

	setup () {
		const event = new CustomEvent( 'partnerUpdate' );

		// Show partner group fields if the user's ownership is less than 100%.
		this.ownership.addEventListener( 'change', ({ target }) => {
			const action = target.value < 100 ? 'add' : 'remove';

			this.partners.forEach( partner => {
				if (
					( action === 'add' && ! partner.classList.contains( 'active' ) ) ||
					( action === 'remove' && partner.classList.contains( 'active' ) )
				) {
					if ( action === 'add' ) {
						partner.removeAttribute( 'disabled' );
						maskInputs( partner );
					} else {
						partner.setAttribute( 'disabled', '' );
						unmaskInputs( partner );
					}
					this.element.dispatchEvent( event );
					window.dispatchEvent( event );
				}

				partner.classList[ action ]( 'active' );
			});
		});

		// Setup button to add more partner group fields.
		if ( this.addButton ) {
			this.addButton.addEventListener( 'click', () => {

				// Prepare the partner group fields.
				const newPartner = this.partners[0].cloneNode( true );
				newPartner.classList.add( 'active', 'removable' );
				newPartner.removeAttribute( 'disabled' );
				[ ...newPartner.querySelectorAll( '*[name]' ) ].forEach( input => {
					input.classList.remove( 'read-only' );
					input.classList.remove( 'input-error' );
					input.name = input.name.replace( /\[(\d+?)\]/g, `[${this.partners.length}]` );
					input.disabled = false;
					input.readOnly = false;
					input.value = null;
				});

				// Mask the cloned inputs.
				maskInputs( newPartner );

				// Append the new partner group fields after the last group.
				this.partners[ this.partners.length - 1 ].after( newPartner );
				this.partners.push( newPartner );
				this.element.dispatchEvent( event );
			});
		}

		// Setup groups remove button.
		this.element.addEventListener( 'click', ({ target }) => {
			const clickedPartner = target.closest( '.js-partner' );

			if (
				target.classList.contains( 'js-title' ) &&
				clickedPartner.classList.contains( 'removable' )
			) {
				this.partners = this.partners.filter( partner => ! partner.isSameNode( clickedPartner ) );
				clickedPartner.remove();
				this.element.dispatchEvent( event );
			}
		});

		// Init dynamically added components.
		this.element.addEventListener( 'partnerUpdate', () => setupComponents() );

		// Unmask inputs on submit.
		// Important for dynamically added inputs.
		if ( this.form ) this.form.addEventListener( 'submit', () => unmaskInputs( this.form ) );
	}
}
