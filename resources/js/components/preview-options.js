import { debounce } from 'lodash';

export default class {
	constructor ( element ) {
		this.element = element;
		this.iframes = this.element.querySelectorAll( '.js-iframe' );
	}

	scaleIframe ( iframe ) {
		const iframeWrapper = iframe.closest( '.js-iframe-wrapper' );
		iframeWrapper.style.height = 'auto';
		iframeWrapper.style.height = `${iframe.contentDocument.querySelector( '.js-dashboard' ).clientHeight}px`;
	}

	setup () {
		this.iframes.forEach( iframe => {
			iframe.onload = () => {
				const toRemove = [ 'header', 'footer', 'sidebar' ];

				// Remove sidebar/header/footer from iframes to provide a cleaner preview of the user view.
				toRemove.forEach( part => {
					iframe.contentDocument.querySelector( `.js-${part}` ).remove();
				});

				// Scale iframe on page load.
				this.scaleIframe( iframe );
			};
		});

		// Scale iframe on window resize.
		window.addEventListener( 'resize', debounce(
			() => this.iframes.forEach( iframe => this.scaleIframe( iframe ) ),
			200
		) );
	}
}
