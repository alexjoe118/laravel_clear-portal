import { maskInputs } from '~/helpers/mask-inputs';
import { setupComponents } from '~/domain/components';

// Setup the page.
export function setupPage ( pages ) {
	window.page = {};
	const page = document.querySelector( '.js-page' );

	// Setup components.
	setupComponents();

	// Mask inputs on page load.
	maskInputs();

	for ( const pageSlug in pages ) {
		if ( page.classList.contains( pageSlug ) ) {

			// Setup page.
			const pageInstance = new pages[ pageSlug ]( page );
			pageInstance.setup();

			// Make page available globally.
			window.page = { ...pageInstance, ...window.page };

			const event = new CustomEvent( 'pageSetup' );
			window.dispatchEvent( event );
		}
	}
}
