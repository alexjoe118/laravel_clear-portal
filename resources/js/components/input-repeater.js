export default class {
	constructor ( element ) {
		this.element = element;
	}

	setup () {
		this.element.addEventListener( 'click', ({ target }) => {

			// Remove input when clicking the remove button.
			if ( target.classList.contains( 'js-button-remove' ) ) {
				target.closest( '.js-input-wrapper' ).remove();
			}

			// Append new repeater item.
			if ( target.classList.contains( 'js-repeater-button' ) ) {

				// Clone the first input and clean its value.
				const input = this.element.querySelector( '.js-input-wrapper' ).cloneNode( true );
				const inputElement = input.querySelector( 'input' );
				inputElement.value = '';

				// Append cloned input to the repeater.
				target.before( input );
			}
		});
	}
}
