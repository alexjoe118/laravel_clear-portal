import SignaturePad from 'signature_pad';

export default class {
	constructor ( element ) {
		this.element = element;
		this.pad = null;
		this.canvas = this.element.querySelector( 'canvas' );
		this.input = this.element.querySelector( '.js-input' );
		this.clearButton = this.element.querySelector( '.js-button-clear' );
	}

	clear () {
		this.pad.clear();
		this.input.value = '';
	}

	resize ( clear = false ) {
		const ratio =  Math.max( window.devicePixelRatio || 1, 1 );
		this.canvas.width = this.canvas.offsetWidth * ratio;
		this.canvas.height = this.canvas.offsetHeight * ratio;
		this.canvas.getContext( '2d' ).scale( ratio, ratio );

		if ( clear ) this.clear();
	}

	sleep ( ms ) {
		return new Promise( resolve => setTimeout( resolve, ms ) );
	}

	async setup () {

		// Initialize the signature pad.
		this.pad = new SignaturePad( this.canvas, { minDistance: 0 });

		await this.sleep( 1 );

		this.resize();

		// Check whether the pad has value or not.
		if ( this.input.value ) {
			const prefix = this.input.value.startsWith( 'data:image' ) ? '' : 'data:image/png;base64,';
			this.pad.fromDataURL( prefix + this.input.value );
		}

		// Check whether the pad is disabled or not.
		if ( this.input.disabled ) {
			this.pad.off();
			return;
		}

		// Update the input with the drew signature.
		this.pad.addEventListener( 'endStroke', () => {
			this.input.value = this.pad.toDataURL();
			this.input.dispatchEvent( new Event( 'change' ) );
		});

		// Setup the clear button.
		this.clearButton.addEventListener( 'click', () => this.clear() );

		// Adjust the canvas on window resize.
		window.addEventListener( 'orientationchange', () => this.resize( true ) );
		window.addEventListener( 'partnerUpdate', async () => {
			await this.sleep( 100 );
			this.resize();
		} );
	}
}
