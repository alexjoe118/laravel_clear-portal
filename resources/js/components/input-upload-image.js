export default class {
	constructor ( element ) {
		this.element = element;
		this.input = this.element.querySelector( '.js-input' );
	}

	setup () {
		this.img = this.element.querySelector( '.js-img' );
		this.preview = this.element.querySelector( '.js-preview' );

		this.input.addEventListener( 'change', () => {
			const file = this.input.files[0];
			const reader = new FileReader;

			reader.onload = () => {
				this.preview.src = reader.result;
			};

			reader.readAsDataURL( file );
		});
	}
}
