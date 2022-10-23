export default class {
	constructor ( element ) {
		this.element = element;
		this.input = this.element.querySelector( '.js-input' );
		this.inputValue = this.element.querySelector( '.js-input-value' );
	}

	setup () {

		// Add true/false value to checkbox.
		this.input.addEventListener( 'change', () => {
			this.inputValue.value = this.input.checked ? '1' : '0';
		});
	}
}
